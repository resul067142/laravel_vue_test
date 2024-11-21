<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MembersTableSeeder extends Seeder
{
    public function run()
    {
        $members = [
            // Ebeveynler (parents)
            [
                'name' => 'Mehmet',
                'role' => 'Baba',
                'image' => null,
                'details' => 'Ailenin babası ve başarılı bir mühendistir.',
                'birth_date' => '1970-01-01',
                'occupation' => 'Mühendis',
                'hobbies' => ['Balık Tutma', 'Satranç'],
                'contact' => 'mehmet@example.com',
                'social_media' => 'https://twitter.com/mehmet',
                'generation' => 'parents',
            ],
            [
                'name' => 'Elif',
                'role' => 'Anne',
                'image' => null,
                'details' => 'Ailenin annesi ve yetenekli bir öğretmendir.',
                'birth_date' => '1972-05-05',
                'occupation' => 'Öğretmen',
                'hobbies' => ['Okuma', 'Yoga'],
                'contact' => 'elif@example.com',
                'social_media' => 'https://facebook.com/elif',
                'generation' => 'parents',
            ],
            // Çocuklar (children)
            [
                'name' => 'Ahmet',
                'role' => 'Oğul',
                'image' => null,
                'details' => 'Üniversite öğrencisi ve basketbol oyuncusu.',
                'birth_date' => '1995-10-10',
                'occupation' => 'Öğrenci',
                'hobbies' => ['Basketbol', 'Bilgisayar Oyunları'],
                'contact' => 'ahmet@example.com',
                'social_media' => 'https://instagram.com/ahmet',
                'generation' => 'children',
            ],
            [
                'name' => 'Ayşe',
                'role' => 'Kız',
                'image' => null,
                'details' => 'Lise öğrencisi ve sanata meraklı.',
                'birth_date' => '1998-12-12',
                'occupation' => 'Öğrenci',
                'hobbies' => ['Resim', 'Piyano'],
                'contact' => 'ayse@example.com',
                'social_media' => 'https://instagram.com/ayse',
                'generation' => 'children',
            ],
            // Akrabalar (relatives)
            [
                'name' => 'Mustafa',
                'role' => 'Amca',
                'image' => null,
                'details' => 'Doktor ve fotoğrafçılığa ilgi duyar.',
                'birth_date' => '1975-03-15',
                'occupation' => 'Doktor',
                'hobbies' => ['Koşu', 'Fotoğrafçılık'],
                'contact' => 'mustafa@example.com',
                'social_media' => 'https://linkedin.com/in/mustafa',
                'generation' => 'relatives',
            ],
            [
                'name' => 'Fatma',
                'role' => 'Teyze',
                'image' => null,
                'details' => 'Avukat ve seyahat etmeyi sever.',
                'birth_date' => '1978-07-20',
                'occupation' => 'Avukat',
                'hobbies' => ['Seyahat', 'Yemek Yapma'],
                'contact' => 'fatma@example.com',
                'social_media' => 'https://twitter.com/fatma',
                'generation' => 'relatives',
            ],
            // Diğer fake veriler...
        ];

        foreach ($members as $memberData) {
            // Hobileri JSON olarak kaydet
            $memberData['hobbies'] = json_encode($memberData['hobbies']);

            Member::create($memberData);
        }
    }
}
