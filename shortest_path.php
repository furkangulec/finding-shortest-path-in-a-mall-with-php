<?php



include 'mysql.php';

// This function checks if the given point is valid
function is_valid_point($node, $grid, $grid_width, $grid_height) {

    $x_coordinate = $node[0];
    $y_coordinate = $node[1];

    if ($x_coordinate >= 0 &&
    $y_coordinate >= 0 &&
      $x_coordinate < $grid_width &&
      $y_coordinate < $grid_height &&
      $grid[$y_coordinate][$x_coordinate] != 'X'
    ) {
      return true;
    }
    return false;
}


// This function converts 2D coordinates to 1D coordinates
function convert2dto1d($x_coordinate, $y_coordinate, $grid_width)
{      
    return $y_coordinate * $grid_width + $x_coordinate;
}

// This function converts 1D coordinates to 2D coordinates
function convert1dto2d($node1d, $grid_width) {
    if ($grid_width == 0) {
      return [-1, -1];
    }

    $y = (int)($node1d / $grid_width);
    $x = $node1d - $y * $grid_width;

    return [$x, $y];
}


// This function finds the shortest path from a point to another point
function path_finder($grid, array $start_position, array $end_position)
{   
    // Gets the width and height of the matrix
    $grid_width = count($grid[0]); 
    $grid_height = count($grid);

    
    // Gets the X and Y coordinates of start and end points
    $start_position_x_coordinate = $start_position[0];
    $start_position_y_coordinate = $start_position[1];

    $end_position_x_coordinate = $end_position[0];
    $end_position_y_coordinate = $end_position[1];

    // If the value is a letter, it converts it to ASCII value
    if (!is_numeric($start_position_x_coordinate))
    {
        $start_position_x_coordinate = ord($start_position_x_coordinate) - 65;
    }

    if (!is_numeric($end_position_x_coordinate))
    {   
        $end_position_x_coordinate = ord($end_position_x_coordinate) - 65;
    }

    // Fills an array with -1 values as much as the grid length
    $came_from = array_fill(0, $grid_width * $grid_height, -1);

    // Creates a queue
    $queue = new SplQueue();

    // Finds the 1D order of the starting point
    $convert = convert2dto1d($start_position_x_coordinate, $start_position_y_coordinate, $grid_width);

    // Adds the starting point to the queue
    $queue->enqueue($convert);

    // When the queue is empty, the loop ends
    while (!$queue->isEmpty()) {
        
        // Gets the first element of the queue
        $current = $queue->dequeue();

        // Gets the X and Y coordinates of the first element of the queue
        $convert_1d_to_2d = convert1dto2d($current, $grid_width);

        // Gets the neighbours of the X and Y coordinates
        $neighbours = find_neighbours($convert_1d_to_2d[0], $convert_1d_to_2d[1], $grid, $grid_width, $grid_height);
  
        // For each neighbour
        foreach ($neighbours as $node) {

            // Gets the X and Y coordinates of the neighbour
            $x_coordinate = $node[0];
            $y_coordinate = $node[1];

            // Find the 1D order of the neighbour
            $node1d = convert2dto1d($x_coordinate, $y_coordinate, $grid_width);
            
            // If the node is not processed before
            if ($came_from[$node1d] == -1) {
                // The neighbour is added to the queue
                $queue->enqueue($node1d);
                // And the 1D order of this neighbour is marked as the current point
                $came_from[$node1d] = $current;
            }
        }
      }

    $path_exists = true;
    

    // Create a queue to find the path
    $path_queue = new SplQueue();

    // Gets the X and Y coordinates of the end point
    $end_node = [$end_position_x_coordinate, $end_position_y_coordinate];

    // The point to go is added to the queue
    $path_queue->enqueue($end_node);
      while (
        // The loop continues until the X and Y coordinates of the end point are equal to the X and Y coordinates of the starting point
        $end_node[0] != $start_position_x_coordinate || $end_node[1] != $start_position_y_coordinate
      ) {

        // Find the 1D order of the point to go
        $end_node_1d = convert2dto1d($end_node[0], $end_node[1], $grid_width);
        
        // If the 1D order of the point to go is -1, that is, if there is no way to go, the loop is exited
        if ($came_from[$end_node_1d] == -1) {
          $path_exists = false;
          break;
        }
        
        // The order of the point reached in the one-dimensional plane is marked as the current point
        $end_node = convert1dto2d($came_from[$end_node_1d], $grid_width);
        // The current point is added to the queue
        $path_queue->push($end_node);
     
      }



    
      $path = [];
      if ($path_exists) {
        while (!$path_queue->isEmpty()) {
          $point = $path_queue->dequeue();
          $path[] = $point;
        }
      }

    return $path;

}


function find_neighbours($x_coordinate, $y_coordinate, $grid, $grid_width, $grid_height)
{   
    // A queue is created to hold the neighbours
    $neighbours = new SplQueue();


    for ($dx = -1; $dx <= 1; $dx += 2) {
        $neighbour_node = [$x_coordinate + $dx, $y_coordinate];
        if (is_valid_point($neighbour_node, $grid, $grid_width, $grid_height)) {
          $neighbours->enqueue($neighbour_node);
        }
    }

    for ($dy = -1; $dy <= 1; $dy += 2) {
        $neighbour_node = [$x_coordinate, $y_coordinate + $dy];
        if (is_valid_point($neighbour_node, $grid, $grid_width, $grid_height)) {
          $neighbours->enqueue($neighbour_node);
        }
    }
  
      return $neighbours;


}








// Create the grid of the mall
function create_mall_grid($mall_id)
{
    $stores = get_stores($mall_id);
    $closed_points = get_closed_points($mall_id);
    $kiosks = get_kiosks($mall_id);
    $mall = get_mall_from_id($mall_id);
    $elevators = get_elevators($mall_id);
    $stairs = get_stairs($mall_id);

    $max_x_coordinate = $mall['max_x_coordinate']; // This comes as a letter
    $max_y_coordinate = $mall['max_y_coordinate'];

    $max_x_coordinate = ord($max_x_coordinate) - 64;

    $floor_count = 0;

    foreach ($stores as $key => $value) {
        if ($value['floor'] > $floor_count) {
            $floor_count = $value['floor'];
        }
    }


    $grids = array();

    for ($i=0; $i < $floor_count; $i++) { 
        $grids[$i] = array();

        for ($j=0; $j < $max_y_coordinate; $j++) { 
            for ($k=0; $k < $max_x_coordinate; $k++) { 
                $grids[$i][$j][$k] = '0';
            }
        }

        // The closed points are marked as X
        foreach ($closed_points as $key => $value) {
            if ($value['floor'] == $i + 1) {
                $value['x_coordinate'] = ord($value['x_coordinate']) - 65;
                $value['y_coordinate'] = $value['y_coordinate'] - 1;
                $grids[$i][$value['y_coordinate']][$value['x_coordinate']] = 'X';
            }
        }
    
        // Stores are marked with their names
        foreach ($stores as $key => $value) {
            if ($value['floor'] == $i + 1) {
                $value['x_coordinate'] = ord($value['x_coordinate']) - 65;
                $value['y_coordinate'] = $value['y_coordinate'] - 1;
                $grids[$i][$value['y_coordinate']][$value['x_coordinate']] = $value['name'];
            }
        }

        // Kiosks are marked with K and their ids
        foreach ($kiosks as $key => $value) {
            if ($value['floor'] == $i + 1) {
                $value['x_coordinate'] = ord($value['x_coordinate']) - 65;
                $value['y_coordinate'] = $value['y_coordinate'] - 1;
                $grids[$i][$value['y_coordinate']][$value['x_coordinate']] = 'K' . $value['id'];
            }
        }

        // Elevators are marked with A
        foreach ($elevators as $key => $value) {
                $value['x_coordinate'] = ord($value['x_coordinate']) - 65;
                $value['y_coordinate'] = $value['y_coordinate'] - 1;
                $grids[$i][$value['y_coordinate']][$value['x_coordinate']] = 'A'; 
        }

        // Stairs are marked with M
        foreach ($stairs as $key => $value) {
                $value['x_coordinate'] = ord($value['x_coordinate']) - 65;
                $value['y_coordinate'] = $value['y_coordinate'] - 1;
                $grids[$i][$value['y_coordinate']][$value['x_coordinate']] = 'M'; 
        }


    }

 
    return $grids;

}

// Converts the path array to text, the letter coordinate that comes as a number is converted back to a letter
function path_to_text(array $path)
{   
    $string = '';
    foreach ($path as $key => $value) {
        $x_coordinate = $value[0] + 65;
        $y_coordinate = $value[1] + 1;

        $string = $string . chr($x_coordinate) . $y_coordinate . ' - ';
    
    }

    $string = substr($string, 0, -3);
    echo "<br>";
    echo "<span style='margin-left: 30px;'>". $string . "</span><br>";
}


// Visualizes the grid
function visualize_grid($matrix, $path = [], $floor)
{   
    $pixel = 25;

    $stores = get_stores();
    $kiosks = get_kiosks();

    $matrix_length = count($matrix);

    $matrix_width = count($matrix[0]);
    

    // Create the image
    $image = imagecreatetruecolor($matrix_width * $pixel + 60, $matrix_length * $pixel + 60);

    // Color definitions
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $red = imagecolorallocate($image, 255, 0, 0);
    $green = imagecolorallocatealpha($image, 0, 255, 0, 50);
    $blue = imagecolorallocate($image, 0, 0, 255);
    $grey = imagecolorallocate($image, 128, 128, 128);

    // Set the background to white
    imagefill($image, 0, 0, $white);

    // Floor name is written
    imagestring($image, $pixel, 30, 5, "Floor: " . $floor, $black);

    // Write each element on the image and draw a square
    for ($i = 0; $i < $matrix_width; $i++) {
        
        for ($j = 0; $j < $matrix_length; $j++) {
            $x = $i * $pixel + 30;
            $y = $j * $pixel + 30;
            $value = $matrix[$j][$i];


            if ($value == 'X') {
                // Closed points are marked with red
                imagefilledrectangle($image, $x-5, $y-5, $x+5, $y+5, $red);
            }

            // Kiosks are marked with blue
            foreach ($kiosks as $key => $kiosk_id) {
               
                if ($value == 'K' . $kiosk_id['id']) {
                    imagefilledrectangle($image, $x-5, $y-5, $x+5, $y+5, $blue);
                }
            }

            
            // Stores are marked with grey
            foreach ($stores as $key => $store_id) {
               
                if ($value == $store_id['name']) {
                    imagefilledrectangle($image, $x, $y, $x+$pixel, $y+$pixel, $grey);
                }
            }

           
            // The path to go is marked with green
            foreach ($path as $key => $coordinate) {
               
                if ($i == $coordinate[0] && $j == $coordinate[1]) {

                    imagefilledrectangle($image, $x-5, $y-5, $x + 5, $y + 5, $green);
                }
            }



            // Replace 0 and X values with space for map cleanliness
            if ($value === '0' || $value == "X")
            {
                $value = ' ';
            }
           
            

            imagestring($image, $pixel/10, $x+$pixel/4, $y+($pixel/5), $value, $black); // write the value of each element in the square
            
        
            if ($i != $matrix_width - 1 && $j != $matrix_length - 1) {
                imagerectangle($image, $x, $y, $x+$pixel, $y+$pixel, $black); // draw a square for each element

            }
          

        }
    }


    return $image;
            
}

?>