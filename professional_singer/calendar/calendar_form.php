<!DOCTYPE html>
<html lang="en">
 <style>
 
 /* Style for the card */
    .card {$query11 = $con->prepare("SELECT * FROM daily_event WHERE users_id = ? ORDER BY date DESC");

        border: 1px solid #dee2e6; /* Add border */
        border-radius: .25rem; /* Add border radius */
        margin: 20px auto; /* Center the card horizontally */
        
    }

    /* Style for the card header */
    .card-header {
        background-color: deepPink; /* Pink background color */
        color: white; /* White text color */
        padding: 10px; /* Add some padding */
        display: flex; /* Use flexbox for layout */
        justify-content: space-between; /* Space items evenly */
        align-items: center; /* Center items vertically */
		
    }

    /* Style for the back button */
    .btn-back {
        color: white; /* White text color */
        background-color: black; /* Black background color */
        padding: 5px 10px; /* Add padding */
        border: none; /* Remove border */
        cursor: pointer; /* Show pointer cursor */
        text-decoration: none; /* Remove default underline */
    }

    /* Style for the h3 inside card header */
    .card-header h3 {
        font-size: 24px; /* Set font size */
        margin: 0; /* Remove default margin */
    }

    /* Style for the form */
    .form-horizontal {
        margin-top: 20px; /* Add some top margin */
        padding: 20px; /* Add padding */
    }
	.btn.btn-success
	{
		border: none;
    background: pink;
    border-radius: 6px;
    height: 30px;
    width: 100px;
    font-size: 18px;
	}
	.btn.btn-success:hover
	{
		 background:deepPink;
		 color:white;
	}
	
	.backbtn
	{
		 color: white; /* White text color */
        background-color: gray; /* Gray background color */
        padding: 5px 10px; /* Add padding */
        border: none; /* Remove border */
        cursor: pointer; /* Show pointer cursor */
        text-decoration: none; /* Remove default underline */
	}
	.backbtn:hover {
        background-color: black; /* Black hover background color */
    }
	.form-control
	{
		
	}
#rowCountSelect
{
width:120px;
}
 table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }
.row-count-select {
            margin-right: 10px;
        }
		#rowCountSelect
		{
			
			width: 10%;
			height: 34px;
			color: #555;
			background-color: #fff;
			background-image: none;
			border: 1px solid #ccc;
			border-radius: 4px;
		}
   .card .card-pink {
      height: 100vh;
      overflow-x: auto;
      overflow-y: scroll; /* Add this line for vertical scrollbar */
    }
    </style>
<body>
    <form action="" method="post" autocomplete="off">
        <?php
            require("../../connect.php");
            $query11 = $con->query("select * from `daily_event` where singer_type='singer'");
        ?>

        <div class="container">
            <div  class="card card-pink" style="width:95%;margin-left:15px;">
              <div class="card-header">
                <h3 class="card-title"><font size="5"><b>EVENTS LIST</b></font></h3>
        </div>
<br>

                <div class="form-group" style="display:flex;justify-content:flex-end;">

                        <label for="searchTitle">Search Title:</label>
                        <input type="text" class="form-control" id="searchTitle" placeholder="Enter title" oninput="searchByTitle();" style="width:197px; margin-top:-9px;">

                    </div>


                       <div class="selectroooow" style="">
						<label for="rowCountSelect" style="">Show:</label>
                        <select id="rowCountSelect" class="form-control" onchange="changeRowCount()">
                            <option value="10">10</option>
                            <option value="15">50</option>
							<option value="15">100</option>
							<option value="15">500</option>
                            
                        </select>	
		              <a href="/rythm/professional_singer/pdfopenpage.php">
						<img src="/rythm/assets/pdf.png" style="height:40px;width:40px;float:right;">
                      </a>
						  </div>
                        <div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sno</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Title</th>
                                    <th>Organizer</th>
									<th>Songs</th>
                                    <th>Amount</th>
									<th>Recevied Amount</th>
									<th>Pending Amount</th>
                                  <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
								$i=0;
								while ($index = $query11->fetch(PDO::FETCH_ASSOC)) {
                                $i++;
									?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $index['date']; ?></td>
                                        <td><?php echo date('H:i', strtotime($index['time'])); ?></td>
                                        <td onclick="showevtndetails(<?php echo $index['id']; ?>);"><?php echo $index['title']; ?></td>
                                        <td><?php echo $index['organizer']; ?></td>
										<td>
										<<a href="javascript:void(0);"
   onclick="showevtndetails(<?php echo (int)$index['id']; ?>); return false;">
    <img src="/rythm/assets/eyeicnn.png"
         alt="View Details"
         style="height:40px;width:40px;">
</a>
										</td>
                                        <td onclick="paydetails(<?php echo $index['id']; ?>);"><?php echo $index['amount']; ?></td>
										<?php
										$eventid=$index['id'];
										$totalamount=$index['amount'];
										$query12 = $con->query("SELECT *,sum(amount) as totalopaidamt FROM `eventpayment` where eventid='$eventid'");
										while ($data = $query12->fetch(PDO::FETCH_ASSOC)){
											
										$paidtotalamt=$data['totalopaidamt'];
										$pendingamt=$totalamount-$paidtotalamt;
										if($paidtotalamt!='')
											 {
											?>
											<td onclick="receivedpaydetails(<?php echo $index['id']; ?>);" style="color:green;font-weight:600;"><?php echo $paidtotalamt; ?></td>
											<td style="color:red;font-weight:600;"><?php echo $pendingamt; ?></td>
										   <?php
											}
										
										else
										{
										?>
										<td onclick="receivedpaydetails(<?php echo $index['id']; ?>);" style="color:green;font-weight:600;"><?php echo 0; ?></td>
											<td style="color:red;font-weight:600;"><?php echo $pendingamt; ?></td>
										 <?php
										}
										}
										?>
                                       
                                        <td>

                                        <a href="/rythm/professional_singer/save_new_event.php"
   title="Add New Event"
   style="display:inline-block; margin-right:5px; cursor:pointer; text-decoration:none;">
    <span style="
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:25px;
        height:25px;
        background:#ff1493;
        color:white;
        border-radius:50%;
        font-size:22px;
        font-weight:bold;
        line-height:25px;">
        +
    </span>
</a>
                                            
<a href="javascript:void(0);"
   onclick="window.loadEditPage(<?php echo (int)$index['id']; ?>); return false;"
   style="margin-right:5px; cursor:pointer; display:inline-block;">
    <img src="/rythm/assets/edit2.png"
         alt="Edit Icon"
         style="height:27px; width:27px; cursor:pointer;">
</a>

                                            <a href="#" onclick="confirmDelete(<?php echo $index['id']; ?>, '<?php echo $index['title']; ?>')" style="margin-right:5px;">
                                                <img src="/rythm/assets/minusss.png" alt="Delete Icon" style="height: 25px; width: 25px;">
                                            </a>
                                            <a href="#" onclick="showPostponeModal(<?php echo $index['id']; ?>)">
                                                <img src="/rythm/assets/expired (1).png" alt="Postpone Icon" style="height: 25px; width: 25px;">
                                            </a>
											 <a href="#" onclick="addsongs(<?php echo $index['id']; ?>)">
                                                <img src="/rythm/assets/add3.png" alt="Postpone Icon" style="height: 30px; width: 30px;">
                                            </a>
										    <a href="#" onclick="showevtndetails(<?php echo $index['id']; ?>)">
                                                <img src="/rythm/assets/eyeicnn.png" alt="Postpone Icon" style="height:45px;width:45px;">
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

            </div>
        </div>
    </form>

    <script>
        function confirmDelete(id, title) {
            var result = confirm("Are you sure you want to delete the meeting with title: " + title + "?");

            if (result) {
                window.location.href = "deleteold.php?id=" + id;
            }
        }

        function changeRowCount() {
            var rowCount = document.getElementById("rowCountSelect").value;
            document.querySelector('.card-container').style.height = `${rowCount * 40}px`; // Assuming 40px is the height of each row
        }

        function searchByTitle() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchTitle");
            filter = input.value.toUpperCase();
            table = document.querySelector(".table");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[3];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
		


window.loadEditPage = function(id) {
    console.log("Edit clicked. Event ID:", id);

    $.ajax({
        type: "POST",
        url: "/rythm/scheduleedit.php",
        data: { id: id },

        success: function(data) {
            var container = $("main.main-content");

            if (container.length === 0) {
                console.error("Main content container not found.");
                return;
            }

            container.html(data);
        },

        error: function(xhr, status, error) {
            console.error("Edit form loading failed:", error);
            console.error(xhr.responseText);
            alert("Unable to load the edit form.");
        }
    });
};


function loadEventPage(url, id, method = "GET") {
    $.ajax({
        type: method,
        url: url,
        data: { id: id },
        success: function(data) {
            var container = $("main.main-content");

            if (container.length === 0) {
                console.error("Main content container not found.");
                return;
            }

            container.html(data);
            container.css({
                "margin-left": "auto",
                "margin-right": "auto"
            });
        },
        error: function(xhr, status, error) {
            console.error("Event page loading failed:", error);
            console.error("HTTP status:", xhr.status);
            console.error("Server response:", xhr.responseText);
            alert("Unable to load this page. Please check the Console.");
        }
    });
}


function showPostponeModal(id) {
    loadEventPage("/rythm/posponetevents.php", id, "POST");
}

function addsongs(id) {
    loadEventPage("/rythm/addsongsconcept.php", id, "POST");
}

function showevtndetails(id) {
    loadEventPage("/rythm/eventdetailsshow.php", id, "GET");
}


function paydetails(id)
{
	//debugger;
	///alert('jijiij');
    var rightcontent=$("#rightcontent").hide();
	$.ajax({
    type:"GET",
    url:"/rythm/conceptamypay.php?id="+id,
    success:function(data){
        $("#centerconteid").css("marginLeft", "450px");
                $("#centerconteid").html(data);
    //  $("#main_content").html(data);
    }
  })
	
}
function receivedpaydetails(id)
{
	//debugger;
	///alert('jijiij');
    var rightcontent=$("#rightcontent").hide();
	$.ajax({
    type:"GET",
    url:"/rythm/receiveamtdetails.php?id="+id,
    success:function(data){
        $("#centerconteid").css("marginLeft", "450px");
                $("#centerconteid").html(data);
    //  $("#main_content").html(data);
    }
  })
	
}

    $.ajax({
        type: "GET",
        url: "/rythm/eventdetailsshow.php?id=" + encodeURIComponent(id),

        success: function(data) {
            console.log("Event details response received:", data);

            var container = $("#centerconteid");

            if (container.length === 0) {
                console.error("#centerconteid not found on the page.");
                return;
            }

            container.css("margin-left", "450px");
            container.html(data);
        },

        error: function(xhr, status, error) {
            console.error("Event details request failed:", error);
            console.error("HTTP status:", xhr.status);
            console.error("Server response:", xhr.responseText);

            alert("Unable to load event details. Check Console.");
        }
    });

function openEventManagement() {
    var rightcontent = $("#rightcontent").hide();

    $.ajax({
        type: "GET",
        url: "/rythm/professional_singer/addevent.php",
        success: function(data) {
            $("#centerconteid").css("marginLeft", "450px");
            $("#centerconteid").html(data);
        }
    });
}

  </script>
</body>
</html>