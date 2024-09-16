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
}