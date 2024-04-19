<?php
session_start(); 
require_once "db_connection.php";
if(!isset($_SESSION['username'])) {
    header("Location: auth.php");
    exit;
}
if(isset($_POST['logout'])) {
    $_SESSION = array();

    session_destroy();
    header("Location: index.php");
}
if(isset($_GET['x'])){
  $display = "none";
  $success = 'none';
  $error = 'none';
  header("Location: home.php");
}

$id = 0 ;
$display = "none";
$success = 'none';
$error = 'none';

$products = [];
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    $message = "No products found.";
}
$data = json_encode($products);
if(isset($_POST['payment'])){
  $id = $_POST['id'];
  $display = "flex" ;
}
if(isset($_GET['cash'])){
$product_id = $_GET['cash'] ;
$user_id = $_SESSION["id"];
$address = $_SESSION["address"];
$sql = "INSERT INTO orders (product_id, user_id, payment , address) VALUES ('$product_id', '$user_id', 'cash' ,'$address')";
    if ($conn->query($sql) === TRUE) {
      $success = 'flex';
      $display = "none";
    } else {
        $message = "Error placing order: " . $conn->error;
    }
}
require_once('vendor/autoload.php'); 
\Stripe\Stripe::setApiKey('YOUR_STRIPE_SECRET_KEY');
if (isset($_GET['card'])) {
    $productId = $_GET['card']; 

    try {
        $product = getProductFromDatabase($conn,$productId); // You need to implement this function
        if (!$product) {
            echo 'Error: Product not found';
            exit;
        }

        // Create a new Checkout session for the product
        \Stripe\Stripe::setApiKey("YOUR_STRIPE_SECRET_KEY");
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'egp',
                    'product_data' => [
                        'name' => $product['name'],
                    ],
                    'unit_amount' => $product['price'] * 100, // Convert price to cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer_email' => $_SESSION["email"],
            'success_url' => 'http://localhost/AlsonMotoParts/home.php?order='.$productId,
            'cancel_url' => 'http://localhost/AlsonMotoParts/home.php',
        ]);

        // Redirect the user to the Stripe Checkout page
        header("Location: " . $session->url);
        exit;
    } catch (\Stripe\Exception\ApiErrorException $e) {
      $error ="flex";
    }
}

if(isset($_GET['order'])){
  $product_id = $_GET['order'] ;
  $user_id = $_SESSION["id"];
  $address = $_SESSION["address"];
  $sql = "INSERT INTO orders (product_id, user_id, payment , address) VALUES ('$product_id', '$user_id', 'card' ,'$address')";
      if ($conn->query($sql) === TRUE) {
        $success = 'flex';
        $display = "none";
      } else {
          $message = "Error placing order: " . $conn->error;
      }
  }

function getProductFromDatabase($conn, $productId) {

  $productId = filter_var($productId, FILTER_SANITIZE_NUMBER_INT);


  $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
  $stmt->bind_param("i", $productId); 


  $stmt->execute();
  $result = $stmt->get_result();


  $productInfo = $result->fetch_assoc();


  $stmt->close();

  return $productInfo;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/master.css" />
    <link rel="icon" type="image/x-icon" href="img/logo.jpg" />
    <title>Alson MotoParts</title>
  </head>

  <body>
  <div class="payment" style="display: <?php echo $display; ?>">
    <div class="contain">
    <a  href="?x" class="x">X</a>
    <a href="?cash=<?php echo $id; ?>" class="buy" ><span class="text">buy it with cash</span></a>
    <a href="?card=<?php echo $id; ?>" class="buy"><span class="text">buy it with card</span></a>
    </div>
  </div>
  <div class="payment" style="display: <?php echo $success; ?>">
    <div class="contain">
    <a  href="?x" class="x">X</a>
    <img src="img/success.jpg" >
    <p>your order will delivered in <?php echo $_SESSION["address"];?> soon</p>
    </div>
  </div>
  <div class="payment" style="display: <?php echo $error; ?>">
    <div class="contain">
    <a  href="?x" class="x">X</a>
    <img src="img/error.jpg" >
    <p>please conect to internet and try again</p>
    </div>
  </div>
    <div class="landing">
      <div class="overlay"></div>
      <div class="con">
        <div class="header">
          <a href="/AlsonMotoParts/home.php"><img src="img/logo.jpg" class="logo" alt="logo" /></a>
          <div class="links-con">
            <ul class="link">
              <li><a href="#about-us">About us</a></li>
              <li><a href="#products">products </a></li>
              <li><a href="#team">our team </a></li>
              <i class="fa-solid fa-caret-up arr"></i>
            </ul>
            <i class="fa-solid fa-bars men"></i>
          </div>
          <div class="logOut-section">
          <form method="post">
                            <button type="submit" name="logout">Log out</button>
                        </form>
          </div>
        </div>
      </div>
      <div class="intro">
        <div>
          <h1>
            Rev up your ride with
            <p>Alson MotoParts</p>
            Your destination for high-quality motorcycle spare parts.
          </h1>
        </div>
      </div>
    </div>
    <div class="con">
      <div class="about-us" id="about-us">
        <div class="info-box">
          <h2>About us</h2>
          <p>
            At Alson MotoParts, we're enthusiasts just like you, passionate
            about motorcycles and dedicated to providing top-quality spare parts
            for riders everywhere. With years of experience in the industry, we
            understand the importance of reliable components to keep your ride
            running smoothly and safely. Our mission is simple: to offer an
            extensive selection of genuine and aftermarket parts, coupled with
            exceptional customer service, to ensure you find exactly what you
            need for your motorcycle. Whether you're a seasoned rider or just
            starting out, trust us to be your go-to destination for all your
            motorcycle spare part needs. Welcome to the ride of reliability with
            Alson MotoParts.
          </p>
        </div>
        <div class="Ab-imgs">
          <img src="img/about-us.jpg" />
        </div>
      </div>
    </div>
    <div class="team" id="team">
      <div class="con fl">
        <h3>our team</h3>
        <div class="work-box">
          <img src="img/t2.jpg" alt="" />
          <h4>Youseef Hesham Abdul aziz</h4>
        </div>
        <div class="work-box">
          <img src="img/t1.jpg" alt="" />
          <h4>Yousef Mostafa Abdul aziz</h4>
        </div>
        <div class="work-box">
          <img src="img/t3.jpg" alt="" />
          <h4>Youssef Mohamed Youssef</h4>
        </div>
        <div class="work-box">
          <img src="img/t4.jpg" alt="" />
          <h4>Yousif Mahmoud mahdy</h4>
        </div>
        <div class="work-box">
          <img src="img/t5.jpg" alt="" />
          <h4>Youssef Mohammed Nabwe</h4>
        </div>
        <div class="work-box">
          <img src="img/t6.jpg" alt="" />
          <h4>Youssef Mohamed Mohamed</h4>
        </div>
      </div>
    </div>
    <div class="products" id="products">
      <div class="con">
        <h2>our products</h2>
        <div class="card-container">
        <?php if(isset($message)) { ?>
                <p><?php echo $message; ?></p>
            <?php } else { ?>
                    <?php foreach($products as $product) { ?>
                      <div class="card">
            <form class="card-flip" method="post">
              <div class="card-back face">
                <p><?php echo $product['price']; ?>EGP</p>
                <input type="number" name="id" value="<?php echo $product['id']; ?>"/>
                <button type="submit" name="payment">buy now</button>
              </div>
              <div class="card-front face">
                <img src="img/<?php echo $product['image']; ?>" alt="product one" />
                <h3><?php echo $product['name']; ?></h3>
                <p>
                <?php echo $product['description']; ?>
                </p>
              </div>
            </form>
          </div>
                    <?php } ?>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <footer>created by love group 177</footer>
  </body>
  <script>
    var data = <?php echo $data ?>
  </script>
</html>
