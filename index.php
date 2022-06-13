<?php
session_start();
//load inventory array into session memory this will act a databse for this program 
//name = Name:$_SESSION['inventory'][0][0]
//description = Decription:$_SESSION['inventory'][0][1]...etc
//inventory table name,description,price,qty
if(!isset($_SESSION['inventory'])){
    $inventory = array (
        array("Mug","Handmade ceramic mug",2.95,10),
        array("Tea Cup","Handmade ceramic tea cup",2.95,10),
        array("Plate","Handmade ceramic plate",2.95,10),
        array("Bowl","Handmade ceramic bowl",2.95,10),
        array("Vase","Handmade ceramic Vase",2.95,10)
    
    );

    $_SESSION['inventory']=$inventory;
}
// ---------------------------
// ----functions----

//display
function display(){
     //display all items as a list with an option to delte or edit
     $display_block=<<<_END
     <div class="new">
         <form class="form" id="new-item" action="index.php" method="post">
                             
             <input type="text"   placeholder="name" name="name" id="name" size="30">
             <input type="text"   placeholder="Brief Description" name="description" id="description" size="30">
             <input type="number" id="price" name="price" placeholder="price" step="0.01" min="1">
             <input type="number" id="quantity" name="qty" placeholder="quantity" min="1">
             <input type="submit" id="new" name="newItem" value="Add New">
         </form>
     </div>

_END;
     for($x=0; $x<count($_SESSION['inventory']); $x++)
     {
         $name=$_SESSION['inventory'][$x][0];
         $description=$_SESSION['inventory'][$x][1];
         $price=$_SESSION['inventory'][$x][2];
         $qty=$_SESSION['inventory'][$x][3];

         $display_block.=<<<_END
         
             
             <div class="entries">
                 <div class="item">
                     <div>
                     <h2>
                     $name
                     </h2>
                     </div>
                     <div>
                     <p>$description<p>
                     </div>
                     <div>
                     $price euro
                     </div>
                     <div>
                     in stock  x$qty
                     </div>
                 </div>
                 <div class="options">
                     <form class="button" id="add-shipment"  action="index.php" method="post">
                             <input type="number" id="quantity" name="quantity" min="1" max="$qty">
                             <input type="submit" id=$x name="addShipment" value="Add to Shipment">
                             <input type=hidden name="itemID" value=$x>
                         </form>
                     <form class="button" id="edit-item" action="index.php" method="post">
                             <input type="submit" id=$x name="confirmEdit" value="Edit">
                             <input type=hidden name="arrayID" value=$x>
                         </form>
                         <form class="button" id="delete-item" action="index.php" method="post">
                             <input type="submit" id=$x name="confirmDelete" value="delete">
                             <input type=hidden name="arrayID" value=$x>
                         </form>
                        
                 </div> 
             </div>
             <br><br>

_END;
     }

    return $display_block;
}

//add to databse

 function addDB($a,$b,$c,$d){
      //check all fields have been entered
      if($a=="" || $b=="" || $b=="" || $d==""){
        $message="PLease fill all boxes";
        return $message;
    }
    else{
        array_push($_SESSION['inventory'], array("$a","$b",$c,$d) );
        $message="New Item added";
        return $message;
    }  
 }

//edit databse

    function editDB($a,$b,$c,$d){
        $_SESSION['inventory'][$_POST['arrayID']][0]=$a;
        $_SESSION['inventory'][$_POST['arrayID']][1]=$b;
        $_SESSION['inventory'][$_POST['arrayID']][2]=$c;
        //amount adds to current inventory
        $_SESSION['inventory'][$_POST['arrayID']][3]=$_SESSION['inventory'][$_POST['arrayID']][3]+$d;
        $message="Update Successful";
        return $message;
    }

//delete from database

 function deletDB($x,$name){
    unset($_SESSION['inventory'][$x]);
    $_SESSION['inventory']=array_values($_SESSION['inventory']);
    $message="<p>$name has been Deleted</p>";
    return $message;
 }

//display shipment
 function displayShip(){
    if(!isset($_SESSION['shipment'])){
        $shipment_display="<p>No Items in shipment<p> ";
}
    else{
        //display current shipment
        $shipment_display=<<<_END
        <div class="shipment">
            <p>Curent Shipment</p>
            <ul>
_END;
        for($x=0; $x<count($_SESSION['shipment']); $x++){
            $shipName=$_SESSION['shipment'][$x][0];
            $shipQuant=$_SESSION['shipment'][$x][1];
            $shipment_display.="<li>$shipName, X $shipQuant</li>";
        }

        $shipment_display.=<<<_END
        </ul></div>
        <div class="options">
            <form class="button" id="order" action="index.php" method="post">
                    <input type="submit" name="placeShipment" value="Place Shipment">
                    
                </form>
                <form class="button" id="cancel-order" action="index.php" method="post">
                    <input type="submit" name="cancelShipment" value="Cancel Shipment">
                
                </form>
            
                
        </div>
        <br><br> 
_END;
    }
    return $shipment_display;
}
//add to shipment
 function addSHip($a,$b,$c){
         //get variable from shipment post
         $id=$a; //$_POST['itemID'];
         $name=$b; //$_SESSION['inventory'][$id][0];
         $qty=$c; //$_POST['quantity'];
         //check if shipment is in session if not create and display
         if(!isset($_SESSION['shipment'])){
             $shipment=array(
                 array($name,$qty)
             );
             $_SESSION['shipment']=$shipment;
             //update inventory
             $_SESSION['inventory'][$id][3]=$_SESSION['inventory'][$id][3]-$qty;
         }
         else{
             //get variable from shipment post
             $id=$a; //$_POST['itemID'];
             $name=$b; //$_SESSION['inventory'][$id][0];
             $qty=$c; //$_POST['quantity'];
             //check is item in shipments
             $inShip=false;
             for($x=0; $x<count($_SESSION['shipment']); $x++){
                     if($name==$_SESSION['shipment'][$x][0]){
                         $shipmentID=$x;
                         $inShip=true;
                     }
             }
     
             if($inShip==true){
                 //update shipments
                 $_SESSION['shipment'][$shipmentID][1]=$_SESSION['shipment'][$shipmentID][1]+$qty;
                 //update inventory
                 $_SESSION['inventory'][$id][3]=$_SESSION['inventory'][$id][3]-$qty;
             }
             else{
                //if false add to shipments
                 array_push($_SESSION['shipment'], array("$name","$qty"));
                 
                 $_SESSION['inventory'][$id][3]=$_SESSION['inventory'][$id][3]-$qty;
             }
     
         }
 }

         
 
 

//commit shipment

 function placeShip(){
    unset($_SESSION['shipment']);
    $message="<p>Shipment has been ordered</p>";
    return $message;
 }

//cancel shipment

 function cancelShip(){
    for($x=0; $x<count($_SESSION['shipment']); $x++){
        for($y=0; $y<count($_SESSION['inventory']); $y++){
            if($_SESSION['inventory'][$y][0]==$_SESSION['shipment'][$x][0]){
                $_SESSION['inventory'][$y][3]=$_SESSION['inventory'][$y][3]+$_SESSION['shipment'][$x][1];
            }
        }
}
    unset($_SESSION['shipment']);
    $message="<p>Shipment has been cancelled</p>";
    return $message;
    
 }

// ---------------------------
///set display mode for switch statement based on what button is pressed
if(isset($_POST['newItem'])){
    $mode="newItem";
}
elseif(isset($_POST['confirmEdit'])){
    $mode="confirmEdit";
}
elseif(isset($_POST['edit'])){
    $mode="edit";
}
elseif(isset($_POST['confirmDelete'])){
    $mode="confirmDelete";
}
elseif(isset($_POST['delete'])){
    $mode="delete";
}
elseif(isset($_POST['addShipment'])){
    $mode="addShipment";
}
elseif(isset($_POST['placeShipment'])){
    $mode="placeShipment";
}
elseif(isset($_POST['cancelShipment'])){
    $mode="cancelShipment";
}
else{
    $mode="display";
    
}
//display 
switch($mode)
{
    case "display":
       $display_block=display();
    break;
    case "newItem":
        //pull variables from POST request
        $name=$_POST['name'];
        $desc=$_POST['description'];
        $price=$_POST['price'];
        $qty=$_POST['qty'];
        $message=addDB($name,$desc,$price,$qty);
        $display_block=display();
    break;
    case "confirmDelete":
        //get varibles from post
        $x=$_POST['arrayID'];
        $name=$_SESSION['inventory'][$x][0];
        
        //display confirmation to delete
        $display_block=<<<_END
        <p>Are you sure want to delete $name changes are permanent.</p>
        <div class="options">
            <form class="button" id="delete-item" action="index.php" method="post">
                <input type="submit" id="$x" name="delete" value="Delete">
                <input type=hidden name="arrayID" value=$x>
            </form>
            <form class="button" id="go-back" action="index.php" method="post">
                <input type="submit"  value="Go Back">
            </form>
        </div>
        
    
    _END;

    break;
    case "delete":
        //get varibles from post
        $x=$_POST['arrayID'];
        $name=$_SESSION['inventory'][$x][0];
        $message=deletDB($x,$name);
        $display_block=display();
    break;
    case "confirmEdit":
        $x=$_POST['arrayID'];

        $name=$_SESSION['inventory'][$x][0];
        $description=$_SESSION['inventory'][$x][1];
        $price=$_SESSION['inventory'][$x][2];
        $qty=$_SESSION['inventory'][$x][3];
    
        $display_block=<<<_END
            <p>All changes are permanent.</p>
            <form class="form" id="new-item" action="index.php" method="post">                    
                <input type="text"  aria-label="name"  name="name" id="name" size="30" value="$name">
                <input type="text"  aria-label="description"  name="description" id="description" size="30" value="$description">
                <input type="text"  aria-label="price"  name="price" id="price" size="30" value="$price">
                <input type="text"  aria-label="quantity" name="qty" id="qty" size="30" placeholder="Add to Inventory">
                <input type="submit" id="edit" name="edit" value="Update">
                <input type=hidden name="arrayID" value=$x>
            </form>
            <form class="button" id="go-back" action="index.php" method="post">
                <input type="submit"  value="Go Back">
            </form>
    
    _END;
        break;
    case "edit":
        //get varibles from post
        $a=$_POST['name'];
        $b=$_POST['description'];
        $c=$_POST['price'];;
        $d=$_POST['qty'];
        $message=editDB($a,$b,$c,$d);
        $display_block=display();
    break;
    case "addShipment":
        //get variables from post
        $x=$_POST['itemID'];
        $y=$_SESSION['inventory'][$x][0];
        $z=$_POST['quantity'];
        //add to shipment
        addSHip($x,$y,$z);
        $display_block=display();
    break;
    case "placeShipment":
        $message=placeShip();
        $display_block=display();
    break;
    case "cancelShipment":
        $message=cancelShip();
        $display_block=display();

    break;
}



?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopify Intern Challenge</title>
    <link rel="stylesheet" href="css.css">
</head>
<body>
<header>
    <h1>Company Name Here</h1>
</header>
<div class="shipment">
    <?php 
        $shipment=displayShip();
        echo $shipment;
    ?> 
</div>
<div class="message">
<?php
if(isset($message)){
    echo $message; 
}


?>
</div>
<div class="main">
<?php echo $display_block; ?>
</div>


</body>
</html>