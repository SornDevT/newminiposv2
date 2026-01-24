<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'ເຄື່ອງດື່ມ', 'description' => 'ນໍ້າດື່ມ, ນໍ້າອັດລົມ, ນໍ້າໝາກໄມ້'],
            ['name' => 'ອາຫານແຫ້ງ', 'description' => 'ເຂົ້າສານ, ເຂົ້າໜົມປັງ, ບະໝີ່'],
            ['name' => 'ອາຫານສົດ', 'description' => 'ຜັກ, ຊີ້ນ, ປາ, ໝາກໄມ້'],
            ['name' => 'ເຄື່ອງໃຊ້ໃນຄົວເຮືອນ', 'description' => 'ສະບູ, ນໍ້າຢາລ້າງຈານ, ຜົງຊັກຟອກ'],
            ['name' => 'ເຄື່ອງສໍາອາງ', 'description' => 'ແປ້ງ, ລິບສະຕິກ, ຄີມບໍາລຸງຜິວ'],
            ['name' => 'ເຄື່ອງຂຽນ', 'description' => 'ປຶ້ມ, ສໍ, ບິກ, ຢາງລຶບ'],
            ['name' => 'ເຄື່ອງໃຊ້ຫ້ອງການ', 'description' => 'ເຈ້ຍ, ເຄື່ອງພິມ, ປາກກາ'],
            ['name' => 'ເຄື່ອງໄຟຟ້າ', 'description' => 'ໂທລະສັບ, ຄອມພິວເຕີ, ພັດລົມ'],
            ['name' => 'ເຄື່ອງນຸ່ງຫົ່ມ', 'description' => 'ເສື້ອ, ໂສ້ງ, ກະໂປງ'],
            ['name' => 'ຮອງຕີນ', 'description' => 'ເກີບ, ແຕະ'],
            ['name' => 'ເຄື່ອງກິລາ', 'description' => 'ບານເຕະ, ແບດມິນຕັນ, ເຄື່ອງອອກກຳລັງກາຍ'],
            ['name' => 'ເຄື່ອງຫຼິ້ນເດັກນ້ອຍ', 'description' => 'ຕຸກກະຕາ, ລົດຂອງຫຼິ້ນ'],
            ['name' => 'ຢາປົວພະຍາດ', 'description' => 'ຢາພື້ນຖານ, ວິຕາມິນ'],
            ['name' => 'ສັດລ້ຽງ', 'description' => 'ອາຫານສັດ, ເຄື່ອງຫຼິ້ນສັດ'],
            ['name' => 'ເຄື່ອງມືຊ່າງ', 'description' => 'ຄ້ອນ, ຄີມ, ໄຂຄວງ'],
            ['name' => 'ນໍ້າມັນເຊື້ອໄຟ', 'description' => 'ນໍ້າມັນລົດ, ນໍ້າມັນກາຊວນ'],
            ['name' => 'ເຄື່ອງປະດັບ', 'description' => 'ສາຍຄໍ, ແຫວນ, ຕຸ້ມຫູ'],
            ['name' => 'ປຶ້ມ', 'description' => 'ປຶ້ມນິທານ, ປຶ້ມຮຽນ'],
            ['name' => 'ເຄື່ອງສຽງ', 'description' => 'ລຳໂພງ, ຫູຟັງ'],
            ['name' => 'ເຄື່ອງດົນຕີ', 'description' => 'ກີຕ້າ, ຄີບອດ'],
            ['name' => 'ອຸປະກອນຕັ້ງຄ້າຍ', 'description' => 'ເຕັ້ນ, ຖົງນອນ'],
            ['name' => 'ອາຫານສຳເລັດຮູບ', 'description' => 'ອາຫານກະປ໋ອງ, ອາຫານແຊ່ແຂງ'],
            ['name' => 'ເຄື່ອງໃຊ້ສ່ວນຕົວ', 'description' => 'ແປງສີຟັນ, ຢາສີຟັນ'],
            ['name' => 'ເຄື່ອງດື່ມບໍາລຸງສຸຂະພາບ', 'description' => 'ນໍ້າໝາກໄມ້ສະກັດ, ຊາສະຫມຸນໄພ'],
            ['name' => 'ຜະລິດຕະພັນກະສິກຳ', 'description' => 'ຝຸ່ນ, ເມັດພັນ'],
            ['name' => 'ອາຫານຫວ່າງ', 'description' => 'ມັນຝຣັ່ງທອດ, ຄຸກກີ້'],
            ['name' => 'ເຄື່ອງໃຊ້ໃນສວນ', 'description' => 'ຊ້ວານ, ຄາດ'],
            ['name' => 'ອຸປະກອນເສີມລົດຍົນ', 'description' => 'ຢາງລົດ, ນໍ້າມັນເຄື່ອງ'],
            ['name' => 'ຂອງທີ່ລະນຶກ', 'description' => 'ຂອງຂວັນ, ຂອງທີ່ລະນຶກຈາກການທ່ອງທ່ຽວ'],
            ['name' => 'ເຄື່ອງໃຊ້ສຳລັບເດັກອ່ອນ', 'description' => 'ຜ້າອ້ອມ, ນົມຜົງ'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
