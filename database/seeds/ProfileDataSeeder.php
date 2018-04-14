<?php
use Illuminate\Database\Seeder;

class ProfileDataSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Profile categories
        DB::table('profile_categories')->insert([
            [
                'cname' => 'Uncategorised',
                'profile_cat_id' => null,
                'deletable' => '0',
                'display_order' => '0',
            ],
            [
                'cname' => 'Personal Information',
                'profile_cat_id' => null,
                'deletable' => '0',
                'display_order' => '2',
            ],
            [
                'cname' => 'Contact Information',
                'profile_cat_id' => null,
                'deletable' => '0',
                'display_order' => '1',
            ],
            [
                'cname' => 'Financial Information',
                'profile_cat_id' => null,
                'deletable' => '1',
                'display_order' => '3',
            ],
            [
                'cname' => 'Home Address',
                'profile_cat_id' => '3',
                'deletable' => '0',
                'display_order' => '1',
            ],
            [
                'cname' => 'Mailing Address',
                'profile_cat_id' => '3',
                'deletable' => '1',
                'display_order' => '2',
            ],
            [
                'cname' => 'Emergency Contact',
                'profile_cat_id' => '3',
                'deletable' => '1',
                'display_order' => '3',
            ],
        ]);

        DB::table('profile_elements')->insert(
            [
                [
                    'id' => '1',
                    'profile_cat_id' => '2',
                    'ename' => 'First Name',
                    'etype' => 'short',
                    'visibility' => 'required',
                    'editable' => '0',
                    'deletable' => '0',
                    'display_order' => '1',
                ],
                [
                    'id' => '2',
                    'profile_cat_id' => '2',
                    'ename' => 'Last Name',
                    'etype' => 'short',
                    'visibility' => 'required',
                    'editable' => '0',
                    'deletable' => '0',
                    'display_order' => '2',
                ],
                [
                    'id' => '3',
                    'profile_cat_id' => '2',
                    'ename' => 'Date of Birth',
                    'etype' => 'short',
                    'visibility' => 'required',
                    'editable' => '0',
                    'deletable' => '0',
                    'display_order' => '3',
                ],
                [
                    'id' => '4',
                    'profile_cat_id' => '2',
                    'ename' => 'Sex',
                    'etype' => 'list',
                    'visibility' => 'required',
                    'editable' => '0',
                    'deletable' => '0',
                    'display_order' => '4',
                ],
                [
                    'id' => '5',
                    'profile_cat_id' => '3',
                    'ename' => 'Email',
                    'etype' => 'medium',
                    'visibility' => 'required',
                    'editable' => '0',
                    'deletable' => '0',
                    'display_order' => '1',
                ],
                [
                    'id' => '6',
                    'profile_cat_id' => '2',
                    'ename' => 'Age',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '0',
                    'deletable' => '0',
                    'display_order' => '5',
                ],
                [
                    'id' => '7',
                    'profile_cat_id' => '3',
                    'ename' => 'Mobile',
                    'etype' => 'short',
                    'visibility' => 'required',
                    'editable' => '0',
                    'deletable' => '0',
                    'display_order' => '2',
                ],
                [
                    'id' => '111',
                    'profile_cat_id' => '2',
                    'ename' => 'id',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '0',
                    'deletable' => '0',
                    'display_order' => '0',
                ],
                [
                    'id' => '110',
                    'profile_cat_id' => '2',
                    'ename' => 'Status',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '0',
                    'deletable' => '0',
                    'display_order' => '0',
                ],
                [
                    'id' => '109',
                    'profile_cat_id' => '2',
                    'ename' => 'Performance',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '0',
                    'deletable' => '0',
                    'display_order' => '0',
                ],
                [
                    'id' => '108',
                    'profile_cat_id' => '2',
                    'ename' => 'id2',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '0',
                    'deletable' => '0',
                    'display_order' => '0',
                ],
                [
                    'id' => '8',
                    'profile_cat_id' => '5',
                    'ename' => 'Address',
                    'etype' => 'medium',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '0',
                    'display_order' => '1',
                ],
                [
                    'id' => '9',
                    'profile_cat_id' => '5',
                    'ename' => 'Unit',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '0',
                    'display_order' => '2',
                ],
                [
                    'id' => '10',
                    'profile_cat_id' => '5',
                    'ename' => 'City',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '0',
                    'display_order' => '3',
                ],
                [
                    'id' => '11',
                    'profile_cat_id' => '5',
                    'ename' => 'State',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '0',
                    'display_order' => '4',
                ],
                [
                    'id' => '12',
                    'profile_cat_id' => '5',
                    'ename' => 'Postcode',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '0',
                    'display_order' => '5',
                ],
                [
                    'id' => '13',
                    'profile_cat_id' => '6',
                    'ename' => 'Address',
                    'etype' => 'medium',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '1',
                ],
                [
                    'id' => '14',
                    'profile_cat_id' => '6',
                    'ename' => 'Unit',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '2',
                ],
                [
                    'id' => '15',
                    'profile_cat_id' => '6',
                    'ename' => 'City',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '3',
                ],
                [
                    'id' => '16',
                    'profile_cat_id' => '6',
                    'ename' => 'State',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '4',
                ],
                [
                    'id' => '17',
                    'profile_cat_id' => '6',
                    'ename' => 'Zipcode',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '5',
                ],
                [
                    'id' => '18',
                    'profile_cat_id' => '7',
                    'ename' => 'Name',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '1',
                ],
                [
                    'id' => '19',
                    'profile_cat_id' => '7',
                    'ename' => 'Relationship',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '2',
                ],
                [
                    'id' => '20',
                    'profile_cat_id' => '7',
                    'ename' => 'Phone',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '3',
                ],
                [
                    'id' => '21',
                    'profile_cat_id' => '2',
                    'ename' => 'Middle Name',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '6',
                ],
                [
                    'id' => '22',
                    'profile_cat_id' => '2',
                    'ename' => 'Alias',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '7',
                ],
                [
                    'id' => '23',
                    'profile_cat_id' => '2',
                    'ename' => 'Ethnicity',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '8',
                ],
                [
                    'id' => '24',
                    'profile_cat_id' => '2',
                    'ename' => 'Height',
                    'etype' => 'list',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '9',
                ],
                [
                    'id' => '25',
                    'profile_cat_id' => '2',
                    'ename' => 'Weight',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '10',
                ],
                [
                    'id' => '30',
                    'profile_cat_id' => '2',
                    'ename' => 'Hip',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '15',
                ],
                [
                    'id' => '31',
                    'profile_cat_id' => '2',
                    'ename' => 'Waist',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '16',
                ],
                [
                    'id' => '32',
                    'profile_cat_id' => '2',
                    'ename' => 'Shoe',
                    'etype' => 'short',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '17',
                ],
                [
                    'id' => '33',
                    'profile_cat_id' => '2',
                    'ename' => 'Eye Colour',
                    'etype' => 'list',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '18',
                ],
                [
                    'id' => '34',
                    'profile_cat_id' => '2',
                    'ename' => 'Hair Colour',
                    'etype' => 'list',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '19',
                ],
                [
                    'id' => '35',
                    'profile_cat_id' => '2',
                    'ename' => 'Hair Length',
                    'etype' => 'list',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '20',
                ],
                [
                    'id' => '36',
                    'profile_cat_id' => '2',
                    'ename' => 'Comments',
                    'etype' => 'long',
                    'visibility' => 'optional',
                    'editable' => '1',
                    'deletable' => '1',
                    'display_order' => '21',
                ],
            ]);

        DB::table('profile_elements')->insert([
            [
                'id' => '26',
                'profile_cat_id' => '2',
                'ename' => 'T-Shirt Size',
                'etype' => 'short',
                'visibility' => 'optional',
                'sex' => 'male',
                'display_order' => '11',
            ],
            [
                'id' => '27',
                'profile_cat_id' => '2',
                'ename' => 'Dress Size',
                'etype' => 'short',
                'visibility' => 'optional',
                'sex' => 'female',
                'display_order' => '12',
            ],
            [
                'id' => '28',
                'profile_cat_id' => '2',
                'ename' => 'Chest',
                'etype' => 'short',
                'visibility' => 'optional',
                'sex' => 'male',
                'display_order' => '13',
            ],
            [
                'id' => '29',
                'profile_cat_id' => '2',
                'ename' => 'Cup Size',
                'etype' => 'short',
                'visibility' => 'optional',
                'sex' => 'female',
                'display_order' => '14',
            ],
        ]);

        // hair length
        DB::table('profile_list_options')->insert([
            [
                'profile_element_id' => '35',
                'option' => "Bald",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '35',
                'option' => "Short",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '35',
                'option' => "Medium",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '35',
                'option' => "Long",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '35',
                'option' => "Other",
                'display_order' => '1',
            ],
        ]);
        // Hair color
        DB::table('profile_list_options')->insert([
            [
                'profile_element_id' => '34',
                'option' => "Black",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '34',
                'option' => "Brown",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '34',
                'option' => "Blonde",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '34',
                'option' => "Red",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '34',
                'option' => "Auburn",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '34',
                'option' => "Other",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '34',
                'option' => "White",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '34',
                'option' => "Grey",
                'display_order' => '1',
            ],
        ]);

        // Eye color
        DB::table('profile_list_options')->insert([
            [
                'profile_element_id' => '33',
                'option' => "Black",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '33',
                'option' => "Brown",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '33',
                'option' => "Blue",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '33',
                'option' => "Green",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '33',
                'option' => "Blue / Green",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '33',
                'option' => "Hazel",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '33',
                'option' => "Other",
                'display_order' => '1',
            ],
        ]);

        // Heights
        DB::table('profile_list_options')->insert([
            [
                'profile_element_id' => '24',
                'option' => "under 4'0 / 122cm",
                'display_order' => '1',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'0 / 122cm",
                'display_order' => '2',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'1 / 124cm",
                'display_order' => '3',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'2 / 127cm",
                'display_order' => '4',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'3 / 130cm",
                'display_order' => '5',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'4 / 132cm",
                'display_order' => '6',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'5 / 135cm",
                'display_order' => '7',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'6 / 137cm",
                'display_order' => '8',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'7 / 140cm",
                'display_order' => '9',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'8 / 142cm",
                'display_order' => '10',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'9 / 145cm",
                'display_order' => '11',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'10 / 147cm",
                'display_order' => '12',
            ],
            [
                'profile_element_id' => '24',
                'option' => "4'11 / 140cm",
                'display_order' => '13',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'0 / 152cm",
                'display_order' => '14',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'1 / 154cm",
                'display_order' => '15',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'2 / 157cm",
                'display_order' => '16',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'3 / 160cm",
                'display_order' => '17',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'4 / 162cm",
                'display_order' => '18',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'5 / 165cm",
                'display_order' => '19',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'6 / 167cm",
                'display_order' => '20',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'7 / 170cm",
                'display_order' => '21',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'8 / 172cm",
                'display_order' => '22',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'9 / 175cm",
                'display_order' => '23',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'10 / 177cm",
                'display_order' => '24',
            ],
            [
                'profile_element_id' => '24',
                'option' => "5'11 / 180cm",
                'display_order' => '25',
            ],
            [
                'profile_element_id' => '24',
                'option' => "6'0 / 183cm",
                'display_order' => '26',
            ],
            [
                'profile_element_id' => '24',
                'option' => "6'1 / 185cm",
                'display_order' => '27',
            ],
            [
                'profile_element_id' => '24',
                'option' => "6'2 / 188cm",
                'display_order' => '28',
            ],
            [
                'profile_element_id' => '24',
                'option' => "6'3 / 190cm",
                'display_order' => '29',
            ],
            [
                'profile_element_id' => '24',
                'option' => "6'4 / 193cm",
                'display_order' => '30',
            ],
            [
                'profile_element_id' => '24',
                'option' => "6'5 / 196cm",
                'display_order' => '31',
            ],
            [
                'profile_element_id' => '24',
                'option' => "6'6 / 198cm",
                'display_order' => '32',
            ],
            [
                'profile_element_id' => '24',
                'option' => "6'7 / 200cm",
                'display_order' => '33',
            ],
            [
                'profile_element_id' => '24',
                'option' => "6'8 / 203cm",
                'display_order' => '34',
            ],
            [
                'profile_element_id' => '24',
                'option' => "6'9 / 206cm",
                'display_order' => '35',
            ],
            [
                'profile_element_id' => '24',
                'option' => "over 6'9 / 206cm",
                'display_order' => '36',
            ],
        ]);

        DB::table('profile_photo_categories')->insert([
            [
                'cname' => 'Headshots',
            ],
            [
                'cname' => 'Fitness',
            ],
        ]);

        DB::table('profile_video_categories')->insert([
            [
                'cname' => 'Showreel',
            ],
            [
                'cname' => 'In Action',
            ],
        ]);

        DB::table('profile_document_categories')->insert([
            [
                'id' => '1',
                'cname' => 'Terms & Agreements',
            ],
            [
                'id' => '2',
                'cname' => 'Forms',
            ],
        ]);
    }
}
