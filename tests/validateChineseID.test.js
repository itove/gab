function validateChineseID(idNumber) {
    // Basic format check
    if (!/^[1-9]\d{5}(18|19|20)\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{3}[\dX]$/.test(idNumber)) {
        return false;
    }

    // Check province code
    const provinceCode = parseInt(idNumber.substring(0, 2));
    const validProvinceCodes = [11,12,13,14,15,21,22,23,31,32,33,34,35,36,37,41,42,43,44,45,46,50,51,52,53,54,61,62,63,64,65];
    if (!validProvinceCodes.includes(provinceCode)) {
        return false;
    }

    // Check birthdate
    const year = parseInt(idNumber.substring(6, 10));
    const month = parseInt(idNumber.substring(10, 12));
    const day = parseInt(idNumber.substring(12, 14));
    const date = new Date(year, month - 1, day);
    if (date.getFullYear() !== year || date.getMonth() + 1 !== month || date.getDate() !== day) {
        return false;
    }

    // Validate checksum
    const weights = [7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2];
    const checksum = '10X98765432';
    let sum = 0;
    for (let i = 0; i < 17; i++) {
        sum += parseInt(idNumber.charAt(i)) * weights[i];
    }
    return idNumber.charAt(17).toUpperCase() === checksum.charAt(sum % 11);
}

describe('validateChineseID', () => {
    test('valid ID number', () => {
        expect(validateChineseID('110101199003077755')).toBe(true);
    });

    test('invalid province code', () => {
        expect(validateChineseID('990101199003077755')).toBe(false);
    });

    test('invalid date', () => {
        expect(validateChineseID('110101199013077755')).toBe(false);
    });

    test('invalid checksum', () => {
        expect(validateChineseID('110101199003077754')).toBe(false);
    });

    test('empty input', () => {
        expect(validateChineseID('')).toBe(false);
    });
});