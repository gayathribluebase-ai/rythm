<!DOCTYPE html>
<html lang="en">
 <style>

echo "var meetingDate = " . json_encode($index['date']) . ";";
echo "var meetingTime = " . json_encode(date('H:i', strtotime($index['time']))) . ";";
echo "var meetingTitle = " . json_encode($index['title']) . ";";
echo "var meetingOrganizer = " . json_encode($index['organizer']) . ";";
echo "var meetingAmount = " . json_encode($index['amount']) . ";";
echo "var meetingSongs = " . json_encode($index['songs'] ?? '') . ";";
echo "var meetingDescription = " . json_encode($index['description'] ?? '') . ";";       
		
  .form-group input[type="checkbox"] {
    margin-left:25px;
}
.btn .btn-danger:hover
{
	background:lightgoldenrodyellow;
}
#label11
{
font-size:25px;
font-weight:500;
	
}
#songname
{
	font-size:22px;
	font-weight:500;
}


<<<<<<< HEAD
=======


>>>>>>> b93e9a2da957229bc0dac5c951e58d496033d7d4
/* === BASE HEADING === */ 

h1 {
  position: relative;
  padding: 0;
  margin: 0;
  font-family: "Raleway", sans-serif;
  font-weight: 300;
  font-size: 40px;
  color: #080808;
  -webkit-transition: all 0.4s ease 0s;
  -o-transition: all 0.4s ease 0s;
  transition: all 0.4s ease 0s;
}

h1 span {
  display: block;
  font-size: 0.5em;
  line-height: 1.3;
}
h1 em {
  font-style: normal;
  font-weight: 600;
}




/* Style 7
   ----------------------------- */
.seven h1 {
text-align: center;
    font-size:30px; 
	font-weight:600; 
	color:deeppink; 
	letter-spacing:1px;
    text-transform: uppercase;
    display: grid;
    grid-template-columns: 1fr max-content 1fr;
    grid-template-rows: 27px 0;
    grid-gap: 20px;
    align-items: center;
}

.seven h1:after,.seven h1:before {
    content: " ";
    display: block;
    border-bottom: 1px solid #c50000;
    border-top: 1px solid #c50000;
    height: 5px;
  background-color:#f8f8f8;
}

.buttonadd
{
	  width:80px;
    border: none;
    border-radius: 8px;
    background: #33cc33;
    color: white;
	margin: 0 43px;


}

.buttonadd:hover
{
	width:80px;
    border: none;
    border-radius: 8px;
    background:green;
    color: white;
	margin: 0 43px;


}

    </style>
<body>
<form action="addsonginsert.php" method="post" autocomplete="off">
        <?php
             require("./connect.php");
			$id=$_REQUEST['id'];

            $query = $con->prepare("SELECT * FROM `daily_event` WHERE id = ?");
$query->execute([$id]);
$index = $query->fetch(PDO::FETCH_ASSOC);
?>

        
		    <input type="hidden" id="conceptid" name="conceptid" value="<?php echo $id;?>">

        <div class="container">
            <div  class="card card-pink">
              <div class="card-header">
                <h3 class="card-title"><font size="5"><b>EVENT POSPONET  </b></font></h3>
				<button type="button" id="postponeBackBtn"
        class="btn btn-danger"
        style="float:right;margin-top:-5px;background:lightyellow;color:black"
        onclick="return_back()">
    Back
</button>
            </div>
	<div style="margin:0 21px;">
    <p id="detailsname" style="font-size:20px;">
    Date: <span id="meeting-date"><?= htmlspecialchars($index['date'] ?? '') ?></span>
</p><br>
    
<p style="font-size:20px;">Time:
    <span id="meeting-time"><?= htmlspecialchars(date('H:i', strtotime($index['time'] ?? '00:00'))) ?></span>
</p>

<p style="font-size:20px;">Title:
    <span id="meeting-title"><?= htmlspecialchars($index['title'] ?? '') ?></span>
</p>

<p style="font-size:20px;">Organizer:
    <span id="meeting-organizer"><?= htmlspecialchars($index['organizer'] ?? '') ?></span>
</p>

<p style="font-size:20px;">Amount:
    <span id="meeting-amount"><?= htmlspecialchars($index['amount'] ?? '') ?></span>
</p>

<p style="font-size:20px;">Songs:
    <span id="meeting-songs"><?= htmlspecialchars($index['songs'] ?? '') ?></span>
</p>

<p style="font-size:20px;">Description:
    <span id="meeting-description"><?= htmlspecialchars($index['description'] ?? '') ?></span>
</p>

	 <label for="time" id="labelllll" style="display:none;color:red;font-size:red;font-weight:600;">You Can't Modify This</label>
    <h4 id="changeDateHeader" style="color:goldenrod;font-weight:600;">Change Date Here <img src="/rythm/assets/downnn.png" style="height:30px;width:30px;"></h4><br>
       <div style="display:flex;">
	   <div class="form-group">
                <label for="date" id="datelavel">Date:</label>
                <input type="date" id="date" name="date">
            </div>

            <div class="form-group" style="margin-left:7vh;">
                <label for="time" id="timelavel">Time:</label>
                <input type="time" id="time" name="time">
            </div>
   </div>
     <button id="posponedButton" type="button" onclick="posponeddfun();">
    Posponed
</button>
       <br>
        </div>
		</div>
 </div>
    </form>

    
<script>
       
function return_back() {
    $.ajax({
        type: "GET",
        url: "/rythm/professional_singer/calendar/calendar_form.php",
        success: function(data) {
            var container = $("#centerconteid");

            if (container.length === 0) {
                container = $(".container").first();
            }

            if (container.length > 0) {
                container.html(data);
                container.css({
                    "margin-left": "auto",
                    "margin-right": "auto"
                });
            } else {
                console.error("Event List container not found.");
            }
        },
        error: function(xhr, status, error) {
            console.error("Back navigation failed:", error);
            console.error("HTTP status:", xhr.status);
            console.error("Response:", xhr.responseText);
        }
    });
}


function posponeddfun() {    

    var conceptid = $('#conceptid').val();
    var date = $('#date').val();
    var time = $('#time').val();

    
var meetingDate = $('#meeting-date').text().trim();
var meetingTime = $('#meeting-time').text().trim();
var meetingTitle = $('#meeting-title').text().trim();
var meetingOrganizer = $('#meeting-organizer').text().trim();
var meetingAmount = $('#meeting-amount').text().trim();
var meetingSongs = $('#meeting-songs').text().trim();
var meetingDescription = $('#meeting-description').text().trim();


    console.log("Concept ID:", conceptid);
    console.log("New date:", date);
    console.log("New time:", time);
    console.log("Original meeting:", {
        date: meetingDate,
        time: meetingTime,
        title: meetingTitle,
        organizer: meetingOrganizer,
        amount: meetingAmount,
        songs: meetingSongs,
        description: meetingDescription
    });
    
    console.log("Submitting postpone request...");

    $.ajax({
        type: "POST",
        url: "/rythm/datetimechange.php",
        data: {
            conceptid: conceptid,
            date: date,
            time: time,
            originalDate: meetingDate,
            originalTime: meetingTime,
            originalTitle: meetingTitle,
            originalOrganizer: meetingOrganizer,
            originalAmount: meetingAmount,
            originalSongs: meetingSongs,
            originalDescription: meetingDescription
        },
        success: function(data) {
            console.log("Server response:", data);

            if (data == 0) {
                alert("Postponed Events successful");
                window.location.assign("/rythm/home.php");
            } else if (data == 1) {
                alert("Something went wrong");
            } else if (data == 2) {
                alert("Cannot update to a past date and time");
            } else if (data == 3) {
                alert("Please choose date and time!");
            } else {
                alert("Unexpected server response. Check Console.");
            }
        },
        
error: function(xhr, status, error) {
    console.error("Postpone request failed:", error);
    console.error("HTTP status:", xhr.status);
    console.error("Server response:", xhr.responseText);

    alert("Postpone failed. Check the Console for details.");
}

    });
}
	
    </script>
</body>
</html>
