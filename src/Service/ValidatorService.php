<?php

namespace App\Service;

class ValidatorService
{
    private const PROVINCE_CODES = [11,12,13,14,15,21,22,23,31,32,33,34,35,36,37,41,42,43,44,45,46,50,51,52,53,54,61,62,63,64,65];
    private const WEIGHTS = [7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2];
    private const CHECKSUM = '10X98765432';

    public function validateChineseID(string $idNumber): bool
    {
        // Basic format check
        if (!preg_match('/^[1-9]\d{5}(18|19|20)\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{3}[\dX]$/', $idNumber)) {
            return false;
        }

        // Check province code
        $provinceCode = (int)substr($idNumber, 0, 2);
        if (!in_array($provinceCode, self::PROVINCE_CODES)) {
            return false;
        }

        // Check birthdate
        $year = (int)substr($idNumber, 6, 4);
        $month = (int)substr($idNumber, 10, 2);
        $day = (int)substr($idNumber, 12, 2);
        if (!checkdate($month, $day, $year)) {
            return false;
        }

        // Validate checksum
        $sum = 0;
        for ($i = 0; $i < 17; $i++) {
            $sum += (int)$idNumber[$i] * self::WEIGHTS[$i];
        }
        return strtoupper($idNumber[17]) === self::CHECKSUM[$sum % 11];
    }
}