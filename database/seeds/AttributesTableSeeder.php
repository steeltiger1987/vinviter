<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent;

class AttributesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attributes')->truncate();

        $page_attributes = [
            ['Venues', 'page.type_group', NULL, NULL],
            ['Organizations', 'page.type_group', NULL, NULL],

            ['Arena', 'page.type', 'type_venue', 1],
            ['Stadium', 'page.type', 'type_venue', 1],
            ['Club', 'page.type', 'type_venue', 1],
            ['Night Club', 'page.type', 'type_venue', 1],
            ['Bar', 'page.type', 'type_venue', 1],
            ['Pub', 'page.type', 'type_venue', 1],
            ['Lounge', 'page.type', 'type_venue', 1],
            ['Event Hall', 'page.type', 'type_venue', 1],
            ['Rooftop Venue', 'page.type', 'type_venue', 1],

            ['Event Organizer', 'page.type', 'type_organization', 2],
            ['Event Planner', 'page.type', 'type_organization', 2],
            ['Event Promoter', 'page.type', 'type_organization', 2],


            ['Any', 'page.activity_period', NULL, NULL],
            ['Weekly', 'page.activity_period', NULL, NULL],
            ['Weekend', 'page.activity_period', NULL, NULL],
            ['Montly', 'page.activity_period', NULL, NULL],
            ['Yearly', 'page.activity_period', NULL, NULL],

            ['All', 'page.season', NULL, NULL],
            ['Winter', 'page.season', NULL, NULL],
            ['Spring', 'page.season', NULL, NULL],
            ['Summer', 'page.season', NULL, NULL],
            ['Autumn', 'page.season', NULL, NULL],
        ];

        $years = range(date('Y'), 1950);
        foreach($years as $year){
            $formatted_years[] = [
                'name'       => $year,
                'type'       => 'page.year',
                'type_info' => NULL,
                'parent_id'  => NULL,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        foreach($page_attributes as $row){
            $formatted_page_attributes[] = [
                'name'       => $row[0],
                'type'       => $row[1],
                'type_info'  => $row[2],
                'parent_id'  => $row[3],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        $formatted_page_attributes = array_merge($formatted_page_attributes, $formatted_years);
        DB::table('attributes')->insert($formatted_page_attributes);





        /* Event attributes */

        $event_group_ids = array();
        $event_groups = [
            ['Most Common Events', 'event.type_group', NULL],
            ['Most Common Parties', 'event.type_group', NULL],
            ['Dance/EDM', 'event.music_group', NULL],
            ['Urban/Black Music', 'event.music_group', NULL],
            ['Alternative', 'event.music_group', NULL],
            ['Christian & Gospel', 'event.music_group', NULL],
            ['Holiday', 'event.music_group', NULL],
        ];

        /* First insert event type groups and get ids */
        foreach($event_groups as $row){
            $event_group_ids[] = DB::table('attributes')->insertGetId([
                'name'       => $row[0],
                'type'       => $row[1],
                'type_info'  => NULL,
                'parent_id'  => $row[2],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $event_attributes = [
            ['Any', 'event.type', NULL],

            ['Event', 'event.type', $event_group_ids[0]],
            ['Festival', 'event.type', $event_group_ids[0]],
            ['Concert', 'event.type', $event_group_ids[0]],
            ['Open-Mic', 'event.type', $event_group_ids[0]],
            ['Pre-Event', 'event.type', $event_group_ids[0]],
            ['Parade', 'event.type', $event_group_ids[0]],
            ['Fundraising', 'event.type', $event_group_ids[0]],
            ['Charity', 'event.type', $event_group_ids[0]],


            ['Birthday Party', 'event.type', $event_group_ids[1]],
            ['Surprise Party', 'event.type', $event_group_ids[1]],
            ['Welcome Party', 'event.type', $event_group_ids[1]],
            ['Reception', 'event.type', $event_group_ids[1]],
            ['Dinner Party', 'event.type', $event_group_ids[1]],
            ['BBQ Party', 'event.type', $event_group_ids[1]],
            ['Block Party', 'event.type', $event_group_ids[1]],
            ['Showers', 'event.type', $event_group_ids[1]],
            ['Graduation Party', 'event.type', $event_group_ids[1]],
            ['Frat Party', 'event.type', $event_group_ids[1]],
            ['House Party', 'event.type', $event_group_ids[1]],
            ['Pre-Party', 'event.type', $event_group_ids[1]],
            ['After-Party', 'event.type', $event_group_ids[1]],
            ['Get Together', 'event.type', $event_group_ids[1]],
            ['Theme Party', 'event.type', $event_group_ids[1]],
            ['Costume Party', 'event.type', $event_group_ids[1]],

            ['Any', 'event.music', NULL],

            ['Pop', 'event.music', $event_group_ids[2]],
            ['Dubstep', 'event.music', $event_group_ids[2]],
            ['Deep-House', 'event.music', $event_group_ids[2]],
            ['Garage', 'event.music', $event_group_ids[2]],
            ['Electronic', 'event.music', $event_group_ids[2]],
            ['Hardcore', 'event.music', $event_group_ids[2]],
            ['House', 'event.music', $event_group_ids[2]],
            ['Tech-House', 'event.music', $event_group_ids[2]],
            ['Techno', 'event.music', $event_group_ids[2]],
            ['Trance', 'event.music', $event_group_ids[2]],

            ['R&B/Hip-Hop', 'event.music', $event_group_ids[3]],
            ['Rap', 'event.music', $event_group_ids[3]],
            ['Old School Rap', 'event.music', $event_group_ids[3]],
            ['Underground Rap', 'event.music', $event_group_ids[3]],
            ['Reggae', 'event.music', $event_group_ids[3]],
            ['Dance hall', 'event.music', $event_group_ids[3]],
            ['Afro', 'event.music', $event_group_ids[3]],

            ['Punk', 'event.music', $event_group_ids[4]],
            ['New Wave', 'event.music', $event_group_ids[4]],
            ['Indie Rock', 'event.music', $event_group_ids[4]],
            ['Hard Rock', 'event.music', $event_group_ids[4]],

            ['Christian Pop', 'event.music', $event_group_ids[5]],
            ['Christian Rap', 'event.music', $event_group_ids[5]],
            ['Christian Rock', 'event.music', $event_group_ids[5]],
            ['Classic Christian', 'event.music', $event_group_ids[5]],
            ['Gospel', 'event.music', $event_group_ids[5]],
            ['Traditional Gospel', 'event.music', $event_group_ids[5]],

            ['Christmas', 'event.music', $event_group_ids[6]],
            ["Christmas: Children's", 'event.music', $event_group_ids[6]],
            ["Christmas: Classical", 'event.music', $event_group_ids[6]],

            ['Free', 'event.entrance', NULL],
            ['Paid', 'event.entrance', NULL],
            ['Tickets Only', 'event.entrance', NULL],

            ['Any', 'event.dress_code', NULL],
            ['Formal', 'event.dress_code', NULL],
            ['Casual', 'event.dress_code', NULL],
            ['Dressy', 'event.dress_code', NULL],
            ['Costume Only', 'event.dress_code', NULL],
            ['Sportive', 'event.dress_code', NULL],
            ['Swimwear', 'event.dress_code', NULL],
            ['White Only', 'event.dress_code', NULL],

            ['Not Required', 'event.document', NULL],
            ['Required', 'event.document', NULL],

            ['Any', 'event.age_limit', NULL],
            ['15+', 'event.age_limit', NULL],
            ['16+', 'event.age_limit', NULL],
            ['17+', 'event.age_limit', NULL],
            ['18+', 'event.age_limit', NULL],
            ['19+', 'event.age_limit', NULL],
            ['20+', 'event.age_limit', NULL],
            ['21+', 'event.age_limit', NULL],
            ['22+', 'event.age_limit', NULL],
            ['23+', 'event.age_limit', NULL],
            ['24+', 'event.age_limit', NULL],
            ['25+', 'event.age_limit', NULL]
        ];

        foreach($event_attributes as $row){
            $formatted_event_attributes[] = [
                'name'       => $row[0],
                'type'       => $row[1],
                'type_info'  => NULL,
                'parent_id'  => $row[2],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        DB::table('attributes')->insert($formatted_event_attributes);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
