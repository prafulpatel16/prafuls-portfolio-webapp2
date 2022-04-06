<?php
//$filename = 'contactus.sql';
getenv('MYSQL_DBHOST') ? $db_host=getenv('MYSQL_DBHOST') : $db_host="127.0.0.0";
getenv('MYSQL_DBPORT') ? $db_port=getenv('MYSQL_DBPORT') : $db_port="3306";
getenv('MYSQL_DBUSER') ? $db_user=getenv('MYSQL_DBUSER') : $db_user="root";
getenv('MYSQL_DBPASS') ? $db_pass=getenv('MYSQL_DBPASS') : $db_pass="";
getenv('MYSQL_DBNAME') ? $db_name=getenv('MYSQL_DBNAME') : $db_name="empdb";

/* Connect to MySQL and select the database. */
$connection = mysqli_connect($db_host, $db_user, $db_pass);

if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

$database = mysqli_select_db($connection, $db_name);

/* Ensure that the EMPLOYEES table exists. */
VerifyContactsTable($connection, $db_name);

/* If input fields are populated, add a row to the EMPLOYEES table. */
$name = htmlentities($_POST['name']);
$email = htmlentities($_POST['email']);
$subject = htmlentities($_POST['subject']);

if (strlen($name) && strlen($email) && strlen($subject)) {
  AddContacts($connection, $name, $email, $subject);
}
?>



<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
<tr>
  <td>NAME</td>
  <td>EMAIL</td>
  <td>SUBJECT</td>
</tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM CONTACTS");

while($query_data = mysqli_fetch_row($result)) {
echo "<tr>";
echo "<td>",$query_data[0], "</td>",
     "<td>",$query_data[1], "</td>",
     "<td>",$query_data[2], "</td>";
echo "</tr>";
}
?>

</table>

<!-- Clean up. -->
<?php

mysqli_free_result($result);
mysqli_close($connection);

?>

</body>
</html>


<?php

/* Add an employee to the table. */
function AddContacts($connection, $name, $email, $subject) {
 $n = mysqli_real_escape_string($connection, $name);
 $e = mysqli_real_escape_string($connection, $email);
 $s = mysqli_real_escape_string($connection, $subject);

 $query = "INSERT INTO CONTACTS (name, email, subject) VALUES ('$n', '$e', '$s');";

 if(!mysqli_query($connection, $query)) echo("<p>Error adding contacts data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyContactsTable($connection, $db_name) {
if(!TableExists("CONTACTS", $connection, $db_name))
{
   $query = "CREATE TABLE CONTACTS (
       
       NAME VARCHAR(45),
       EMAIL VARCHAR(90),
       SUBJECT VARCHAR(90)
     )";

   if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
}
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $db_name) {
$t = mysqli_real_escape_string($connection, $tableName);
$d = mysqli_real_escape_string($connection, $db_name);

$checktable = mysqli_query($connection,
    "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

if(mysqli_num_rows($checktable) > 0) return true;

return false;
}
?>                        
