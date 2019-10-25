<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/SMTP.php';
include 'exempt.php';  

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$name = trim(filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING));
	$email = trim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL));
	$category = trim(filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING));
	$title = trim(filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING));

	//TODO Going to have to put some logic here for the multiple format/genre selects
	$format = "";
	$formatBook = trim(filter_input(INPUT_POST, "format-book", FILTER_SANITIZE_STRING));
	$formatMovie = trim(filter_input(INPUT_POST, "format-movie", FILTER_SANITIZE_STRING));
	$formatMusic = trim(filter_input(INPUT_POST, "format-music", FILTER_SANITIZE_STRING));

	if ($formatBook) {
		$format = $formatBook;
	} else if ($formatMovie) {
		$format = $formatMovie;
	} else if ($formatMusic) {
		$format = $formatMusic;
	} else {
		$format = "None";
	}

	$genre = "";
	$genreBooks = trim(filter_input(INPUT_POST, "genre-books", FILTER_SANITIZE_STRING));
	$genreMovies = trim(filter_input(INPUT_POST, "genre-movies", FILTER_SANITIZE_STRING));
	$genreMusic = trim(filter_input(INPUT_POST, "genre-music", FILTER_SANITIZE_STRING));
	
	if ($genreBooks) {
		$genre = $genreBooks;
	} else if ($genreMovies !== "") {
		$genre = $genreMovies;
	} else if ($genreMusic !== "") {
		$genre = $genreMusic;
	} else {
		$genre = "None";
	}

	$year = trim(filter_input(INPUT_POST, "year", FILTER_SANITIZE_NUMBER_INT));



	$details = trim(filter_input(INPUT_POST, "details", FILTER_SANITIZE_SPECIAL_CHARS));

	if ($name == "" || $email == "" || $category == "" || $title == "") {
		$error_message = "Please fill in the required fields: Name, Email, Category, and Title";		
	}

	if (!isset($error_message) && $_POST["validation"] != "") {
		$error_message = "Bad form input";
	}

	if (!isset($error_message) && !PHPMailer::validateAddress($email)) {
		$error_message = "Invalid Email Address";
	}

	if (!isset($error_message)) {

		$email_body = "";
		$email_body .= "Name " . $name . "\n";
		$email_body .= "Email " . $email . "\n";
		$email_body .= "\n\nSuggested Item\n\n";
		$email_body .= "Category " . $category . "\n";
		$email_body .= "Title " . $title . "\n";
		$email_body .= "Format " . $format . "\n";
		$email_body .= "Genre " . $genre . "\n";
		$email_body .= "Year " . $year . "\n";
		$email_body .= "Details " . $details . "\n";

		$mail = new PHPMailer;
	        // $mail->isSMTP();
	        // $mail->Host = 'localhost';
	        // $mail->Port = 25;
	        // $mail->CharSet = PHPMailer::CHARSET_UTF8;
		$mail->isSMTP();
		//Enable SMTP debugging
		// SMTP::DEBUG_OFF = off (for production use)
		// SMTP::DEBUG_CLIENT = client messages
		// SMTP::DEBUG_SERVER = client and server messages
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		//Set the hostname of the mail server
		$mail->Host = 'smtp.gmail.com';
		// use
		// $mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6
		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = 587;
		//Set the encryption mechanism to use - STARTTLS or SMTPS
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication - use full email address for gmail
		$mail->Username = 'rossjcoop@gmail.com';
		//Password to use for SMTP authentication
		$mail->Password = $gcode;

        //It's important not to use the submitter's address as the from address as it's forgery,
        //which will cause your messages to fail SPF checks.
        //Use an address in your own domain as the from address, put the submitter's address in a reply-to
        $mail->setFrom('rossjcoop@gmail.com', $name);
        $mail->addAddress('rossjcoop@gmail.com');
        $mail->addReplyTo($email, $name);
        $mail->Subject = 'Library suggestion from ' . $name;
        $mail->Body = $email_body;

        if ($mail->send()) {     
            header("location:suggest.php?status=thanks");
            exit;
        } 
        //else statement not necessary since exiting the page if successful!
        $error_message = 'Mailer Error: '. $mail->ErrorInfo;		
	}
}

$pageTitle = 'Suggest a Media Item';
$section = "suggest";


include("inc/header.php"); 




?>



<div class="section page">
	<div class="wrapper">
		<h1>Suggest a Media Item</h1>

		<?php if (isset($_GET["status"]) && $_GET["status"] == "thanks") {
			echo "<p>Thanks for the email! I&rsquo;ll check out your suggestion shortly!</p>";
		} else {
			if (isset($error_message)) {
				echo '<p class="message">'.$error_message.'</p>';
			} else {
				echo '<p>If you think there is something I&rsquo;m missing, let me know! Complete the form to send me an email.</p>';
			}
		?>

		
		<form method="post" action="suggest.php">
			<table>
				<tr>
					<th>
						<label for="name">Name (required)</label>
					</th>
					<td>
						<input type="text" name="name" id="name" value="<?php
						if (isset($name)) echo $name;
						?>"/>
					</td>
				</tr>
				<tr>
					<th>
						<label for="email">Email (required)</label>
					</th>
					<td>
						<input type="text" name="email" id="email" value="<?php
						if (isset($email)) echo $email;
						?>"/>
					</td>
				</tr>
				<tr>
					<th>
						<label for="category">Category (required)</label>
					</th>
					<td>
						<select name="category" id="category">
							<option value="None">Select One</option>
							<option value="Books"<?php
							if (isset($category) && $category == "Books") echo " selected";
							?>>Book</option>
							<option value="Movies"<?php
							if (isset($category) && $category == "Movies") echo " selected";
							?>>Movie</option>
							<option value="Music"<?php
							if (isset($category) && $category == "Music") echo " selected";
							?>>Music</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label for="title">Title (required)</label>
					</th>
					<td>
						<input type="text" name="title" id="title" value="<?php
						if (isset($title)) echo $title;
						?>"/>
					</td>
				</tr>
				<tr class="options" value="None">
					<th>
						<label for="format-none">Format</label>
					</th>
					<td>
						<select id="format-none" name="format-none">
                    		<option value="None">Select One</option>
                    	</select>
                    </td>
                </tr>
                <tr class="options" value="Books">
                	<th>
                		<label for="format-book">Format</label>
                	</th>
                    <td>
                    	<select id="format-book" name="format-book">
	                    	<option value="">Select One</option>
	                        <option value="Audio"<?php
							if (isset($formatBook) && $formatBook == "Audio") echo " selected";
							?>>Audio</option>
	                        <option value="Ebook"<?php
							if (isset($formatBook) && $formatBook == "Ebook") echo " selected";
							?>>Ebook</option>
	                        <option value="Hardback"<?php
							if (isset($formatBook) && $formatBook == "Hardback") echo " selected";
							?>>Hardback</option>
	                        <option value="Paperback"<?php
							if (isset($formatBook) && $formatBook == "Paperback") echo " selected";
							?>>Paperback</option>
                    	</select>
                    </td>
                </tr>
                <tr class="options" value="Movies">
                	<th>
                		<label for="format-movie">Format</label>
                	</th>
                    <td>
                    	<select id="format-movie" name="format-movie">
	                    	<option value="">Select One</option>
	                        <option value="Blu-ray"<?php
							if (isset($formatMovie) && $formatMovie == "Blu-ray") echo " selected";
							?>>Blu-ray</option>
	                        <option value="DVD"<?php
							if (isset($formatMovie) && $formatMovie == "DVD") echo " selected";
							?>>DVD</option>
	                        <option value="Streaming"<?php
							if (isset($formatMovie) && $formatMovie == "Streaming") echo " selected";
							?>>Streaming</option>
	                        <option value="VHS"<?php
							if (isset($formatMovie) && $formatMovie == "VHS") echo " selected";
							?>>VHS</option>
                    	</select>
                	</td>
                </tr>
                <tr class="options" value="Music">
                	<th>
                		<label for="format-music">Format</label>
                	</th>
                	<td>
                    	<select id="format-music" name="format-music">
	                    	<option value="">Select One</option>
	                        <option value="Cassette"<?php
							if (isset($formatMusic) && $formatMusic == "Cassette") echo " selected";
							?>>Cassette</option>
	                        <option value="CD"<?php
							if (isset($formatMusic) && $formatMusic == "CD") echo " selected";
							?>>CD</option>
	                        <option value="MP3"<?php
							if (isset($formatMusic) && $formatMusic == "MP3") echo " selected";
							?>>MP3</option>
	                        <option value="Vinyl"<?php
							if (isset($formatMusic) && $formatMusic == "Vinyl") echo " selected";
							?>>Vinyl</option>
                		</select>
                	</td>
				</tr>
				<tr class="options" value="None">
	                <th>
	                    <label for="genre-none">Genre</label>
	                </th>
	                <td>
	                	<select id="genre-none" name="genre-none">
	                    	<option value="None">Select One</option>
	                	</select>
	                </td>
	            </tr>
	            <tr class="options" value="Books">
	            	<th>
	                    <label for="genre-books">Genre</label>
	                </th>
	                <td>
	                	<select id="genre-books" name="genre-books">
		                    <option value="">Select One</option>	               
	                        <option value="Action"<?php
							if (isset($genreBooks) && $genreBooks == "Action") echo " selected";
							?>>Action</option>
	                        <option value="Adventure"<?php
							if (isset($genreBooks) && $genreBooks == "Adventure") echo " selected";
							?>>Adventure</option>
	                        <option value="Comedy"<?php
							if (isset($genreBooks) && $genreBooks == "Comedy") echo " selected";
							?>>Comedy</option>
	                        <option value="Fantasy"<?php
							if (isset($genreBooks) && $genreBooks == "Fantasy") echo " selected";
							?>>Fantasy</option>
	                        <option value="Historical"<?php
							if (isset($genreBooks) && $genreBooks == "Historical") echo " selected";
							?>>Historical</option>
	                        <option value="Historical Fiction"<?php
							if (isset($genreBooks) && $genreBooks == "Historical Fiction") echo " selected";
							?>>Historical Fiction</option>
	                        <option value="Horror"<?php
							if (isset($genreBooks) && $genreBooks == "Horror") echo " selected";
							?>>Horror</option>
	                        <option value="Magical Realism"<?php
							if (isset($genreBooks) && $genreBooks == "Magical Realism") echo " selected";
							?>>Magical Realism</option>
	                        <option value="Mystery"<?php
							if (isset($genreBooks) && $genreBooks == "Mystery") echo " selected";
							?>>Mystery</option>
	                        <option value="Paranoid"<?php
							if (isset($genreBooks) && $genreBooks == "Paranoid") echo " selected";
							?>>Paranoid</option>
	                        <option value="Philosophical"<?php
							if (isset($genreBooks) && $genreBooks == "Philosophical") echo " selected";
							?>>Philosophical</option>
	                        <option value="Political"<?php
							if (isset($genreBooks) && $genreBooks == "Political") echo " selected";
							?>>Political</option>
	                        <option value="Romance"<?php
							if (isset($genreBooks) && $genreBooks == "Romance") echo " selected";
							?>>Romance</option>
	                        <option value="Saga"<?php
							if (isset($genreBooks) && $genreBooks == "Saga") echo " selected";
							?>>Saga</option>
	                        <option value="Satire"<?php
							if (isset($genreBooks) && $genreBooks == "Satire") echo " selected";
							?>>Satire</option>
	                        <option value="Sci-Fi"<?php
							if (isset($genreBooks) && $genreBooks == "Sci-Fi") echo " selected";
							?>>Sci-Fi</option>
	                        <option value="Tech"<?php
							if (isset($genreBooks) && $genreBooks == "Tech") echo " selected";
							?>>Tech</option>
	                        <option value="Thriller"<?php
							if (isset($genreBooks) && $genreBooks == "Thriller") echo " selected";
							?>>Thriller</option>
	                        <option value="Urban"<?php
							if (isset($genreBooks) && $genreBooks == "Urban") echo " selected";
							?>>Urban</option>
                    	</select>
                    </td>
                </tr>
                <tr class="options" value="Movies">
                	<th>
	                    <label for="genre-movies">Genre</label>
	                </th>
	                <td>
                    	<select id="genre-movies" name="genre-movies">
	                    	<option value="">Select One</option>
	                        <option value="Action"<?php
							if (isset($genreMovies) && $genreMovies == "Action") echo " selected";
							?>>Action</option>
	                        <option value="Adventure"<?php
							if (isset($genreMovies) && $genreMovies == "Adventure") echo " selected";
							?>>Adventure</option>
	                        <option value="Animation"<?php
							if (isset($genreMovies) && $genreMovies == "Animation") echo " selected";
							?>>Animation</option>
	                        <option value="Biography"<?php
							if (isset($genreMovies) && $genreMovies == "Biography") echo " selected";
							?>>Biography</option>
	                        <option value="Comedy"<?php
							if (isset($genreMovies) && $genreMovies == "Comedy") echo " selected";
							?>>Comedy</option>
	                        <option value="Crime"<?php
							if (isset($genreMovies) && $genreMovies == "Crime") echo " selected";
							?>>Crime</option>
	                        <option value="Documentary"<?php
							if (isset($genreMovies) && $genreMovies == "Documentary") echo " selected";
							?>>Documentary</option>
	                        <option value="Drama"<?php
							if (isset($genreMovies) && $genreMovies == "Drama") echo " selected";
							?>>Drama</option>
	                        <option value="Family"<?php
							if (isset($genreMovies) && $genreMovies == "Family") echo " selected";
							?>>Family</option>
	                        <option value="Fantasy"<?php
							if (isset($genreMovies) && $genreMovies == "Fantasy") echo " selected";
							?>>Fantasy</option>
	                        <option value="Film-Noir"<?php
							if (isset($genreMovies) && $genreMovies == "Film-Noir") echo " selected";
							?>>Film-Noir</option>
	                        <option value="History"<?php
							if (isset($genreMovies) && $genreMovies == "History") echo " selected";
							?>>History</option>
	                        <option value="Horror"<?php
							if (isset($genreMovies) && $genreMovies == "Horror") echo " selected";
							?>>Horror</option>
	                        <option value="Musical"<?php
							if (isset($genreMovies) && $genreMovies == "Musical") echo " selected";
							?>>Musical</option>
	                        <option value="Mystery"<?php
							if (isset($genreMovies) && $genreMovies == "Mystery") echo " selected";
							?>>Mystery</option>
	                        <option value="Romance"<?php
							if (isset($genreMovies) && $genreMovies == "Romance") echo " selected";
							?>>Romance</option>
	                        <option value="Sci-Fi"<?php
							if (isset($genreMovies) && $genreMovies == "Sci-Fi") echo " selected";
							?>>Sci-Fi</option>
	                        <option value="Sport"<?php
							if (isset($genreMovies) && $genreMovies == "Sport") echo " selected";
							?>>Sport</option>
	                        <option value="Thriller"<?php
							if (isset($genreMovies) && $genreMovies == "Thriller") echo " selected";
							?>>Thriller</option>
	                        <option value="War"<?php
							if (isset($genreMovies) && $genreMovies == "War") echo " selected";
							?>>War</option>
	                        <option value="Western"<?php
							if (isset($genreMovies) && $genreMovies == "Western") echo " selected";
							?>>Western</option>
	                    </select>
	                </td>
	            </tr>
	            <tr class="options" value="Music">
	            	<th>
	                    <label for="genre-music">Genre</label>
	                </th>
	                <td>
	                    <select id="genre-music" name="genre-music">
		                    <option value="">Select One</option>
	                        <option value="Alternative"<?php
							if (isset($genreMusic) && $genreMusic == "Alternative") echo " selected";
							?>>Alternative</option>
	                        <option value="Blues"<?php
							if (isset($genreMusic) && $genreMusic == "Blues") echo " selected";
							?>>Blues</option>
	                        <option value="Classical"<?php
							if (isset($genreMusic) && $genreMusic == "Classical") echo " selected";
							?>>Classical</option>
	                        <option value="Country"<?php
							if (isset($genreMusic) && $genreMusic == "Country") echo " selected";
							?>>Country</option>
	                        <option value="Dance"<?php
							if (isset($genreMusic) && $genreMusic == "Dance") echo " selected";
							?>>Dance</option>
	                        <option value="Easy Listening"<?php
							if (isset($genreMusic) && $genreMusic == "Easy Listening") echo " selected";
							?>>Easy Listening</option>
	                        <option value="Electronic"<?php
							if (isset($genreMusic) && $genreMusic == "Electronic") echo " selected";
							?>>Electronic</option>
	                        <option value="Folk"<?php
							if (isset($genreMusic) && $genreMusic == "Folk") echo " selected";
							?>>Folk</option>
	                        <option value="Hip Hop/Rap"<?php
							if (isset($genreMusic) && $genreMusic == "Hip Hop/Rap") echo " selected";
							?>>Hip Hop/Rap</option>
	                        <option value="Inspirational/Gospel"<?php
							if (isset($genreMusic) && $genreMusic == "Inspirational/Gospel") echo " selected";
							?>>Inspirational/Gospel</option>
	                        <option value="Jazz"<?php
							if (isset($genreMusic) && $genreMusic == "Jazz") echo " selected";
							?>>Jazz</option>
	                        <option value="Latin"<?php
							if (isset($genreMusic) && $genreMusic == "Latin") echo " selected";
							?>>Latin</option>
	                        <option value="New Age"<?php
							if (isset($genreMusic) && $genreMusic == "New Age") echo " selected";
							?>>New Age</option>
	                        <option value="Opera"<?php
							if (isset($genreMusic) && $genreMusic == "Opera") echo " selected";
							?>>Opera</option>
	                        <option value="Pop"<?php
							if (isset($genreMusic) && $genreMusic == "Pop") echo " selected";
							?>>Pop</option>
	                        <option value="R&B/Soul"<?php
							if (isset($genreMusic) && $genreMusic == "R&B/Soul") echo " selected";
							?>>R&amp;B/Soul</option>
	                        <option value="Reggae"<?php
							if (isset($genreMusic) && $genreMusic == "Reggae") echo " selected";
							?>>Reggae</option>
	                        <option value="Rock"<?php
							if (isset($genreMusic) && $genreMusic == "Rock") echo " selected";
							?>>Rock</option>
	                    </select>
	                </td>                 								              
            	</tr>
            	<tr>
					<th>
						<label for="year">Year</label>
					</th>
					<td>
						<input type="text" name="year" id="year" value="<?php
						if (isset($name)) echo $name;
						?>"/>
					</td>
				</tr>
				<tr>
					<th>
						<label for="details">Additional Details</label>
					</th>
					<td>
						<textarea name="details" id="details"><?php
						if (isset($details)) echo $details;
						?>
						</textarea>
					</td>
				</tr>
				<tr style="display:none">
					<td>
						<input type="text" name="validation" id="validation"/>
						<p>Please leave this field blank</p>
					</td>
				</tr>
			</table>
			<input type="submit" name="" value="Send"/>
		</form>
		<?php } ?>
	</div>
</div>

<script>

	let category = document.getElementById("category");
	let target = document.querySelectorAll(".options");


	const displayWhenSelected = (source, value, target) => {
    	const selectedIndex = source.selectedIndex;
    	const isSelected = source[selectedIndex].value === value;
    	target.classList[isSelected ? "add" : "remove"]("show");
	};

	window.onload = () => {
		target.forEach(tr => {
			displayWhenSelected(category, tr.attributes.value.value, tr);
		});
	};
	
	category.addEventListener("change", (evt) => {
		target.forEach(tr => {
			displayWhenSelected(category, tr.attributes.value.value, tr);
		})
    	
	});	

</script>


<?php include("inc/footer.php"); ?>