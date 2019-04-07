<?php
/*departments array simulate the date comming from the database
 * the data comming from the database need to follow this syntax
 * for be display inside the calandar*/
$departments = [
    [ 'id' => 'a',
        'building' => 'Technical Department',
        'title' => 'Impiccichè Giuseppe' ],
    [ 
        'id' => 'b',
        'building'  => 'Technical Department',
        'title'  => 'Casoni Adrien' ],
    [ 
        'id' => 'c',
        'building'  => 'Administration Department',
        'title' => 'Tae Hee Kim' 
    ]
];

/*function transform the comming data in json api*/
echo json_encode($departments);
?>