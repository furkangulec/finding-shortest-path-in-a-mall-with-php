<?php

include 'shortest_path.php';

$mall_id = 1;
$stores = get_stores($mall_id);
$kiosks = get_kiosks($mall_id);
$closed_points = get_closed_points($mall_id);

$grids = create_mall_grid($mall_id);

foreach ($grids as $key => $grid) {
    $blank_map = visualize_grid($grid, [], $key + 1);
    imagepng($blank_map, 'images/blank_map'. $key .'.png');
    imagedestroy($blank_map);

    echo '<img src="images/blank_map'. $key .'.png?='.time().'">';
   
}

?>


<html>
<head>
	<meta charset="UTF-8" />
	<title>The Mall - Kiosk Project</title>
</head>
<body>




	<form id="select-store" style="margin-left: 30px;">

        <label> Closed points: </label> <?php foreach ($closed_points as $key => $value): ?>
            
            <span style="margin-left: 5px; color: red;"> <?php echo $value['x_coordinate'] . $value['y_coordinate'] . '(floor ' . $value['floor'] . ')' ; ?> </span>


            <?php endforeach; ?>

        <br><br>
		
        <label for="stores">Stores:</label>

        <?php foreach ($stores as $key => $value): ?>
            
            <input checked type="radio" id="<?php echo $value['id']; ?>" name="store" value="<?php echo $value['id']; ?>">
            <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>

        <?php endforeach;?>

        <br><br>

        <label for="kiosks">Kiosks:</label>
        <select name="kiosks" id="kiosks">
        <?php foreach ($kiosks as $key => $value): ?>
            
            <option value="<?php echo $value['id']; ?>">Kiosk <?php echo $value['id']; ?></option>

            <?php endforeach; ?>

        </select>
        
        <br><br>
        <button type="button" id="submit">Get directions</button>

	</form>
		<div></div>
</body>
<script src="jquery-3.0.0.min.js"></script>

<script>
 $(document).ready(function() {
  $('#submit').click(function() {

    // Get selected store value
    var selected_store = $('input[name="store"]:checked').val();
    // Get selected kiosk value
    var selected_kiosk = $('#kiosks').val();

    // Create AJAX request
    $.ajax({
      type: "POST",
      url: "ajax.php",
      data: { selected_store: selected_store, selected_kiosk: selected_kiosk },
      success:function(e){ 
            $("div").html("").html(e); 
        }
    });
  });
});
</script>
</html>