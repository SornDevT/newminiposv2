<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'ແກ້ວ', 'abbreviation' => 'ກ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ມີລັກສະນະເປັນແກ້ວ'],
            ['name' => 'ຂວດ', 'abbreviation' => 'ຂ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ມີລັກສະນະເປັນຂວດ'],
            ['name' => 'ລູກ', 'abbreviation' => 'ລ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ມີລັກສະນະເປັນລູກ'],
            ['name' => 'ອັນ', 'abbreviation' => 'ອ', 'description' => 'ໜ່ວຍນັບທົ່ວໄປ'],
            ['name' => 'ກິໂລ', 'abbreviation' => 'ກກ', 'description' => 'ໜ່ວຍວັດແທກນໍ້າໜັກ'],
            ['name' => 'ກຣາມ', 'abbreviation' => 'ກ', 'description' => 'ໜ່ວຍວັດແທກນໍ້າໜັກ'],
            ['name' => 'ລິດ', 'abbreviation' => 'ລ', 'description' => 'ໜ່ວຍວັດແທກປະລິມານຂອງແຫຼວ'],
            ['name' => 'ມິນລີລິດ', 'abbreviation' => 'ມລ', 'description' => 'ໜ່ວຍວັດແທກປະລິມານຂອງແຫຼວ'],
            ['name' => 'ແມັດ', 'abbreviation' => 'ມ', 'description' => 'ໜ່ວຍວັດແທກຄວາມຍາວ'],
            ['name' => 'ຊັງຕີແມັດ', 'abbreviation' => 'ຊມ', 'description' => 'ໜ່ວຍວັດແທກຄວາມຍາວ'],
            ['name' => 'ໂມງ', 'abbreviation' => 'ຊມ', 'description' => 'ໜ່ວຍວັດແທກເວລາ'],
            ['name' => 'ນາທີ', 'abbreviation' => 'ນທ', 'description' => 'ໜ່ວຍວັດແທກເວລາ'],
            ['name' => 'ວັນ', 'abbreviation' => 'ວ', 'description' => 'ໜ່ວຍວັດແທກເວລາ'],
            ['name' => 'ເດືອນ', 'abbreviation' => 'ດ', 'description' => 'ໜ່ວຍວັດແທກເວລາ'],
            ['name' => 'ປີ', 'abbreviation' => 'ປ', 'description' => 'ໜ່ວຍວັດແທກເວລາ'],
            ['name' => 'ຊອງ', 'abbreviation' => 'ຊ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ບັນຈຸໃນຊອງ'],
            ['name' => 'ແຜ່ນ', 'abbreviation' => 'ຜ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ມີລັກສະນະເປັນແຜ່ນ'],
            ['name' => 'ຫໍ່', 'abbreviation' => 'ຫ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ບັນຈຸໃນຫໍ່'],
            ['name' => 'ແທ່ງ', 'abbreviation' => 'ທ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ມີລັກສະນະເປັນແທ່ງ'],
            ['name' => 'ດອກ', 'abbreviation' => 'ດ', 'description' => 'ໜ່ວຍນັບສຳລັບດອກໄມ້'],
            ['name' => 'ໜ່ວຍ', 'abbreviation' => 'ນ', 'description' => 'ໜ່ວຍນັບທົ່ວໄປ, ມັກໃຊ້ກັບໝາກໄມ້'],
            ['name' => 'ຄູ່', 'abbreviation' => 'ຄ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ມາເປັນຄູ່'],
            ['name' => 'ມັດ', 'abbreviation' => 'ມ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ຖືກມັດລວມກັນ'],
            ['name' => 'ປ່ອງ', 'abbreviation' => 'ປ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ບັນຈຸໃນປ່ອງ'],
            ['name' => 'ຊຸດ', 'abbreviation' => 'ຊ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ມາເປັນຊຸດ'],
            ['name' => 'ລາຍການ', 'abbreviation' => 'ລລ', 'description' => 'ໜ່ວຍນັບສຳລັບລາຍການຕ່າງໆ'],
            ['name' => 'ຫົວ', 'abbreviation' => 'ຫ', 'description' => 'ໜ່ວຍນັບສຳລັບຜັກບາງຊະນິດ'],
            ['name' => 'ກະປ໋ອງ', 'abbreviation' => 'ກປ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ບັນຈຸໃນກະປ໋ອງ'],
            ['name' => 'ແຖວ', 'abbreviation' => 'ຖ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ຈັດລຽງເປັນແຖວ'],
            ['name' => 'ຜືນ', 'abbreviation' => 'ຜ', 'description' => 'ໜ່ວຍນັບສຳລັບສິ່ງຂອງທີ່ມີລັກສະນະເປັນຜືນເຊັ່ນ ຜ້າ'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
