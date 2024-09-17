<?php

namespace Spwa;

class BinaryConverter
{

    private function packInt(int $data): string
    {
        // manually have to check the size and the sign
        if ($data < 0) {
            // signed value
            if ($data >= -128) {
                // signed char
                return pack('Cc', 3, $data);
            }
            if ($data >= -32768) {
                // signed short
                return pack('Cs', 4, $data);
            }
            // signed long
            return pack('Cl', 5, $data);
        }
        // unsigned values
        if ($data < 256) {
            return pack('CC', 0, $data);
        }
        if ($data < 65536)
            return pack('CS', 1, $data);

        return pack('Cq', 2, $data);
    }

    private function packObject(object $data): string
    {
        $className = get_class($data);
        $properties = get_object_vars($data);

        $packedData = $this->packString($className);
        foreach ($properties as $key => $value) {
            $packedData .= $this->pack($key);
            $packedData .= $this->pack($value);
        }
        return $packedData;
    }

    private function packFloat(float $data): string
    {
        if ($data === (float)(float)$data) {
            // Handle as a single-precision float (4 bytes)
            return pack('Cf', 6, $data); // 6 to indicate single-precision float
        }
        // Handle as a double-precision float (8 bytes)
        return pack('Cd', 7, $data); // 9 to indicate double-precision float
    }

    private function packString(string $data): string
    {
        // First, pack the length of the string, then the string itself
        $length = strlen($data);
        return pack('Cl', 8, $length) . $data; // 7 to indicate string type
    }

    private function packArray(array $data): string
    {
        $packedData = pack('Cl', 9, count($data)); // 8 to indicate array type and pack the size
        foreach ($data as $value) {
            $packedData .= $this->pack($value); // Recursively pack each element
        }
        return $packedData;
    }

    public function pack($data): string
    {
        if (is_int($data)) {
            return $this->packInt($data);
        }
        // is_double is an alias of is_float
        if (is_float($data)) {
            return $this->packFloat($data);
        }
        if (is_string($data)) {
            return $this->packString($data);
        }
        if (is_array($data)) {
            return $this->packArray($data);
        }
        if (is_object($data)) {
            return $this->packObject($data);
        }
        return "";
    }

}