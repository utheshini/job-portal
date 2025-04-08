gJobseek - By Mohammad Ahsan Junaid & Abdulrehman
		BS(CIS) 2019 -2023

To run the Application using XAMPP, you can follow these steps:

-----------------------------------------------------------------------------------------------------
		      Install XAMPP 
-----------------------------------------------------------------------------------------------------
1.Download and install XAMPP from the official website (https://www.apachefriends.org/index.html) based on your operating system. 

2.Follow the installation instructions provided for your specific platform.


-----------------------------------------------------------------------------------------------------
		Move the project files 
-----------------------------------------------------------------------------------------------------
1.Navigate to the XAMPP installation directory on your machine. 

2.In the XAMPP installation folder, locate the "htdocs" directory. 

3.Move the entire project folder or the extracted contents into the "htdocs" directory.



-----------------------------------------------------------------------------------------------------
		Configure the database
-----------------------------------------------------------------------------------------------------
1.Open a web browser and go to `http://localhost/phpmyadmin` to access the phpMyAdmin interface provided by XAMPP.


-----------------------------------------------------------------------------------------------------
		   Create a database
-----------------------------------------------------------------------------------------------------
1.In phpMyAdmin, click on the "Databases" tab and enter a name for your new database. 

2.Click the "Create" button to create the database.


-----------------------------------------------------------------------------------------------------
	             Import the database schema
-----------------------------------------------------------------------------------------------------
1.With the newly created database selected, click on the "Import" tab in phpMyAdmin. 

2.Click on the "Choose File" button and navigate to the project folder you moved to the "htdocs" directory. 

3.Look for a file named "jobportal.sql" within the project folder, select it, and click the "Go" button to import the database schema.


-----------------------------------------------------------------------------------------------------
	          Configure database connection 
-----------------------------------------------------------------------------------------------------
1.In the project folder that you moved to the "htdocs" directory, locate a file named "db.php". 

2.Open this file in a text editor.


-----------------------------------------------------------------------------------------------------
	        Modify database connection details
-----------------------------------------------------------------------------------------------------
1.Within the "db.php" file, you should find variables representing the database connection details. 

2.Adjust the values of these variables to match your XAMPP setup, variables to modify currently are:
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jobportal";


-----------------------------------------------------------------------------------------------------
		       Start XAMPP
-----------------------------------------------------------------------------------------------------
1.Open the XAMPP control panel.

2.Start the Apache and MySQL services.


-----------------------------------------------------------------------------------------------------
		Access the job portal 
-----------------------------------------------------------------------------------------------------
1.Open a web browser and enter the following URL: `http://localhost/job-portal`.

2.Now you login as candidate with following details
Email: test1@user.com
Password: 123456 ( All Password are encrpyted through code so you CANNOT change password directly from database.)

3.Now you login as employer with following details

Email: test1@employer.com
Password: 123456 ( All Password are encrpyted through code so you CANNOT change password directly from database.)

4.Now you login as Admin with following details

Username: admin
Password: 123456 ( Password is not encrpyted from code so you CAN change directly from database.)
>>>>>>> 858ed19 (Initial Commit)
