<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_US'); // Using en_US for general names, will adjust for Lao-like names

        $laoNames = [
            'ທ້າວ ສົມສັກ', 'ນາງ ສົມສີ', 'ທ້າວ ບຸນມີ', 'ນາງ ດອກໄມ້', 'ທ້າວ ຄໍາເພັດ',
            'ນາງ ແສງເດືອນ', 'ທ້າວ ສຸລິຍາ', 'ນາງ ຈັນທະລາ', 'ທ້າວ ວິໄລ', 'ນາງ ພອນສຸກ',
            'ທ້າວ ວຽງໄຊ', 'ນາງ ສຸດາ', 'ທ້າວ ເພັດສະໝອນ', 'ນາງ ລັດສະໝີ', 'ທ້າວ ຄຳມ່ວນ',
            'ນາງ ອຳໄພ', 'ທ້າວ ສົມພອນ', 'ນາງ ແສງຄຳ', 'ທ້າວ ດາວັນ', 'ນາງ ຈັນທອນ',
            'ທ້າວ ອານຸສອນ', 'ນາງ ວິໄລພອນ', 'ທ້າວ ວິລະສັກ', 'ນາງ ກັນຍາ', 'ທ້າວ ວັນນະສອນ',
            'ນາງ ສາຍພິນ', 'ທ້າວ ທະນູ', 'ນາງ ມາລາ', 'ທ້າວ ເພັດ', 'ນາງ ຄຳລາ'
        ];

        for ($i = 0; $i < 30; $i++) {
            Customer::create([
                'name' => $faker->randomElement($laoNames) . ' ' . ($i + 1), // Add a number to ensure uniqueness for example
                'phone' => '020' . $faker->randomNumber(8, true), // Generates a 8-digit number
                'address' => 'ບ້ານ ' . $faker->city . ', ເມືອງ ' . $faker->state . ', ແຂວງ ' . $faker->country,
            ]);
        }
    }
}
