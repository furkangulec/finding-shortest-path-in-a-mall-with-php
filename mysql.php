<?php 


// Database connection
$db_host = "localhost";
$db_user = "root";
$db_password = "root";
$db_name = "kiosk_project";
$db_port = "3306";

$db = mysqli_connect($db_host, $db_user, $db_password, $db_name, $db_port);

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

function get_malls() {
    global $db;
    $sql = "SELECT * FROM malls";
    $result = mysqli_query($db, $sql);
    
    $malls = [];

    if ($result)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            $malls[] = $row;
        }
    }
    else
    {
        return False;
    }

    return $malls;
}


function get_mall_from_id(int $id)
{

    global $db;
    $sql = "SELECT * FROM malls WHERE id = '$id'";
    $result = mysqli_query($db, $sql);

    $mall = [];

    if ($result)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            $mall = $row;
        }
    }
    else
    {
        return False;
    }

    return $mall;
}

function get_stores(int $mall_id = 0)
{
    global $db;
    $where = $mall_id > 0 ? "WHERE mall_id = '$mall_id'" : " ";
    $sql = "SELECT * FROM stores $where";
    $result = mysqli_query($db, $sql);

    $stores = [];

    if ($result)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            $stores[] = $row;
        }
    }
    else
    {
        return False;
    }

    return $stores;
}

function get_stairs(int $mall_id = 0)
{
    global $db;
    $where = $mall_id > 0 ? " WHERE mall_id = '$mall_id'" : " ";
    $sql = "SELECT * FROM stairs" . $where;
    $result = mysqli_query($db, $sql);

    $stairs = [];

    if ($result)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            $stairs[] = $row;
        }
    }
    else
    {
        return False;
    }

    return $stairs;
}

function get_elevators(int $mall_id = 0)
{
    global $db;
    $where = $mall_id > 0 ? " WHERE mall_id = '$mall_id'" : " ";
    $sql = "SELECT * FROM elevators" . $where;
    $result = mysqli_query($db, $sql);


    $elevators = [];

    if ($result)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            $elevators[] = $row;

        }
    }
    else
    {
        return False;
    }

    return $elevators;
}


function get_kiosks(int $mall_id = 0)
{
    global $db;
    $where = $mall_id > 0 ? " WHERE mall_id = '$mall_id'" : " ";
    $sql = "SELECT * FROM kiosks $where";
    $result = mysqli_query($db, $sql);

    $kiosks = [];

    if ($result)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            $kiosks[] = $row;
        }
    }
    else
    {
        return False;
    }

    return $kiosks;
}


function get_closed_points($mall_id)
{
    global $db;
    $sql = "SELECT * FROM closed_points WHERE mall_id = '$mall_id'";
    $result = mysqli_query($db, $sql);

    $closed_points = [];

    if ($result)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            $closed_points[] = $row;
        }
    }
    else
    {
        return False;
    }

    return $closed_points;
}


function get_kiosk_point(int $id)
{
    global $db;
    $sql = "SELECT * FROM kiosks WHERE id = '$id'";
    $result = mysqli_query($db, $sql);

    $kiosk = [];

    if ($result)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            $kiosk[] = $row;
        }
    }
    else
    {
        return False;
    }




    $x_coordinate = ord(strtoupper($kiosk[0]['x_coordinate'])) - 65;
    $y_coordinate = $kiosk[0]['y_coordinate'] - 1;

    $point = [$x_coordinate, $y_coordinate];

    return $point;
}

function get_store_point(int $id)
{
    global $db;
    $sql = "SELECT * FROM stores WHERE id = '$id'";
    $result = mysqli_query($db, $sql);

    $store = [];

    if ($result)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            $store[] = $row;
        }
    }
    else
    {
        return False;
    }

    $x_coordinate = ord(strtoupper($store[0]['x_coordinate'])) - 65;
    $y_coordinate = $store[0]['y_coordinate'] - 1;

    $point = [$x_coordinate, $y_coordinate];

    return $point;
}



function get_store_floor_from_id(int $id)
{
    global $db;

    $sql = "SELECT * FROM stores WHERE id = '$id'";
    $result = mysqli_query($db, $sql);
    $store_floor = [];

    if ($result)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            $store_floor = $row['floor'];
        }
    }
    else
    {
        return False;
    }


    return $store_floor;
}

function get_kiosk_floor_from_id(int $id)
{
    global $db;

    $sql = "SELECT * FROM kiosks WHERE id = '$id'";
    $result = mysqli_query($db, $sql);
    $store_floor = [];

    if ($result)
    {
        while ($row = mysqli_fetch_assoc($result))
        {
            $store_floor = $row['floor'];
        }
    }
    else
    {
        return False;
    }


    return $store_floor;
}


?>