<?php 

include "shortest_path.php";

// Get POST data
// If there is no POST data, set default values
$selected_mall = isset($_POST['selected_mall']) ? $_POST['selected_mall'] : 1;
$selected_kiosk = isset($_POST['selected_kiosk']) ? $_POST['selected_kiosk'] : 1;
$selected_store = isset($_POST['selected_store']) ? $_POST['selected_store'] : 1;

$grids = create_mall_grid($selected_mall);

// Find the floor of selected kiosk and store
$store_floor = get_store_floor_from_id($selected_store);
$kiosk_floor = get_kiosk_floor_from_id($selected_kiosk);


if ($store_floor != $kiosk_floor) {
    
    $elevators = get_elevators($selected_mall);
    $stairs = get_stairs($selected_mall);

    $to_elevator_path_size = -1;
    $to_elevator_path_coordinates = [];
    foreach ($elevators as $key => $value) {
        
        $elevator_coordinates = [$value['x_coordinate'], $value['y_coordinate'] - 1];
        
        $to_elevator_path = path_finder($grids[$kiosk_floor-1], get_kiosk_point($selected_kiosk), $elevator_coordinates);

        if (count($to_elevator_path) < $to_elevator_path_size || $to_elevator_path_size == -1) {
            $to_elevator_path_size = count($to_elevator_path);
            $to_elevator_path_coordinates = $elevator_coordinates;
            
        }

    }
    

    $to_stairs_path_size = -1;
    $to_stairs_path_coordinates = [];

    foreach ($stairs as $key => $value) {
        
        $stairs_coordinates = [$value['x_coordinate'], $value['y_coordinate'] - 1];
        $to_stairs_path = path_finder($grids[$kiosk_floor-1], get_kiosk_point($selected_kiosk), $stairs_coordinates);

        if (count($to_stairs_path) < $to_stairs_path_size || $to_stairs_path_size == -1) {
            $to_stairs_path_size = count($to_stairs_path);
            $to_stairs_path_coordinates = $stairs_coordinates;
            
        }

    }

    $grid = $grids[$kiosk_floor-1];


    if ($to_stairs_path_size > 0 || $to_elevator_path_size > 0)
    {
        echo "<span style='margin-left: 30px;'> The store is not on this floor! You need to go to $store_floor. floor! </span> <br>";

        if ($to_elevator_path_size < $to_stairs_path_size) {
            $path = path_finder($grid, get_kiosk_point($selected_kiosk), $to_elevator_path_coordinates);
            $path = array_reverse($path);

            path_to_text($path);
            echo "<br> <span style='margin-left: 30px;'>Take the elevator to $store_floor. floor! </span><br>";
    
            $image = visualize_grid($grid, $path, $kiosk_floor);
    
            imagepng($image, 'images/path-'. $kiosk_floor .'.png');
    
            echo '<br><img src="images/path-'. $kiosk_floor .'.png?='.time().'">';
    
        }
        else {
            $path = path_finder($grid, get_kiosk_point($selected_kiosk), $to_stairs_path_coordinates);
            $path = array_reverse($path);

            path_to_text($path);
            echo "<br> <span style='margin-left: 30px;'>Take the stairs to $store_floor. floor! </span><br>";

            $image = visualize_grid($grid, $path, $kiosk_floor);
    
            imagepng($image, 'images/path-'. $kiosk_floor .'.png');
    
            echo '<img src="images/path-'. $kiosk_floor .'.png?='.time().'">';
    
        }


        $grid = $grids[$store_floor-1];

        if ($to_elevator_path_size < $to_stairs_path_size) {
            $path = path_finder($grid, $to_elevator_path_coordinates, get_store_point($selected_store));
            $path = array_reverse($path);

            path_to_text($path);


            $image = visualize_grid($grid, $path, $store_floor);

            imagepng($image, 'images/path-'. $store_floor .'.png');

            echo '<br><img src="images/path-'. $store_floor .'.png?='.time().'">';

        }
        else {
            $path = path_finder($grid, $to_stairs_path_coordinates, get_store_point($selected_store));
            $path = array_reverse($path);

            path_to_text($path);

            $image = visualize_grid($grid, $path, $store_floor);

            imagepng($image, 'images/path-'. $store_floor .'.png');

            echo '<br><img src="images/path-'. $store_floor .'.png?='.time().'">';

        }




    }
    else
    {
        echo "<script>alert('Unfortunately, the path to the store could not be found!');</script>";
    }


}
else
{

    $grid = $grids[$store_floor-1];   
    
    $path = path_finder($grid, get_kiosk_point($selected_kiosk), get_store_point($selected_store));

    if (count($path) > 0) {

        $path = array_reverse($path);

        path_to_text($path);

        $image = visualize_grid($grid, $path, $store_floor);

        imagepng($image, 'images/path-'. $store_floor .'.png');

        echo '<br><img src="images/path-'. $store_floor .'.png?='.time().'">';     
    }
    else
    {
        echo "<script>alert('Unfortunately, the path to the store could not be found!');</script>";
    }
        
}



?>