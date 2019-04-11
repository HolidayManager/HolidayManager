<?php
/*holidays array simulate the date comming from the database
 * the data comming from the database need to follow this syntax 
 * for be display inside the calandar*/
$holidays = [
    [
        'resourceId' => 'a', /*this id will be replaced by the uuid of the user comming from the database*/
        "title" => 'Holiday',
        'start' => '2019-04-07T12:00:00+00:00',
        'end' => '2019-04-17T12:00:00+00:00'
    ],
    [
        'resourceId' => 'a',
        "title" => 'Holiday',
        'start' => '2019-04-19T12:00:00+00:00',
        'end' => '2019-04-23T12:00:00+00:00'
    ],
    [
        'resourceId' => 'b',
        "title" => 'Seek',
        'start' => '2019-04-13T12:00:00+00:00',
        'end' => '2019-04-18T12:00:00+00:00',
        'color' => 'orange'
    ],
    [
        'resourceId' => 'c',
        "title" => 'Holiday',
        'start' => '2019-04-23T12:00:00+00:00',
        'end' => '2019-04-28T12:00:00+00:00'
    ],
];

/*function transform the comming data in json api*/

echo json_encode($holidays);

?>