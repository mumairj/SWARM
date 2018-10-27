<!DOCTYPE html>
<html lang="en">
   <?php include 'header.php';?>
   <style type="text/css">
      * {
      box-sizing: border-box;
      }
      body {
      font: 16px Arial;  
      }
      .autocomplete {
      /*the container must be positioned relative:*/
      position: relative;
      display: inline-block;
      }
      input {
      border: 1px solid transparent;
      background-color: #f1f1f1;
      padding: 10px;
      font-size: 16px;
      }
      input[type=text] {
      background-color: #f1f1f1;
      width: 100%;
      }
      input[type=submit] {
      background-color: DodgerBlue;
      color: #fff;
      cursor: pointer;
      }
      .autocomplete-items {
      position: absolute;
      border: 1px solid #d4d4d4;
      border-bottom: none;
      border-top: none;
      z-index: 99;
      /*position the autocomplete items to be the same width as the container:*/
      top: 100%;
      left: 0;
      right: 0;
      }
      .autocomplete-items div {
      padding: 10px;
      cursor: pointer;
      background-color: #fff; 
      border-bottom: 1px solid #d4d4d4; 
      }
      .autocomplete-items div:hover {
      /*when hovering an item:*/
      background-color: #e9e9e9; 
      }
      .autocomplete-active {
      /*when navigating through the items using the arrow keys:*/
      background-color: DodgerBlue !important; 
      color: #ffffff; 
      }
  #container {
  min-width: 310px;
  max-width: 100%;
  height: 400px;
  margin: 0 auto
}
      .playerOne {
      float: left;
      }
      .playerTwo {
      float: right;
      }
	  
	  
	  
   </style>
   <!-- http://doc.jsfiddle.net/use/hacks.html#css-panel-hack -->
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <style>
   </style>
   <body class="fix-header fix-sidebar">
      <div id="main-wrapper">
         <!-- header header  -->
         <div class="header">
            <?php include 'nav.php';?>
         </div>
         <!-- End header header -->
         <?php include 'leftside.php';?>
         <!-- Page wrapper  -->
         <div class="page-wrapper">
            <!-- Bread crumb -->
            <div class="row page-titles">
               <div class="col-md-5 align-self-center">
                  <h3 class="text-primary">Advanced</h3>
               </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Advanced</li>
                        <li class="breadcrumb-item active">Problem Sentiments</li>
                    </ol>
                </div>
            </div>
            <!-- End Bread crumb -->
            <!-- Container fluid  -->
            <div class="container-fluid">
               <!-- Start Page Content -->
               <div class="row">
				<!--<button onclick="getDataFromServer('Drugs!');">Test</button>-->
                  <div class="col-md-12">
                     <div class="playerOne">
                        <span class="input-group-btn"><button class="btn btn-primary" type="submit"><i class="ti-search"></i></button></span>
                        <div class="autocomplete">
                           <input id="myInput" type="text" name="myProblem" placeholder="Search Problem">
                        </div>
                     </div>
                     <div class="playerTwo">
                        <div class="dropdown">
                           <button id="dbutton" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Select Team<span class="caret"></span></button>
                           <ul id="dmenue" class="dropdown-menu">
						   <li><a href="#">Select Problem</a></li>
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
       
               <hr />
               <div class="row bg-white m-l-0 m-r-0 box-shadow ">

                  <div class="col-lg-12">
					<div id="container"></div>
                  </div>
        
               </div>
               <!-- End PAge Content -->
            </div>
            <!-- End Container fluid  -->
            <!-- footer -->
            <?php include 'footer.php';?>
            <!-- End footer -->
         </div>
         <!-- End Page wrapper  -->
      </div>
      <?php include 'footerScripts.php';?>
      <script src="code/highcharts.js"></script>
      <script src="code/map.js"></script>
      <script src="https://code.highcharts.com/modules/data.js"></script>
      <script>
         function autocomplete(inp, arr) {
           /*the autocomplete function takes two arguments,
           the text field element and an array of possible autocompleted values:*/
           var currentFocus;
           /*execute a function when someone writes in the text field:*/
           inp.addEventListener("input", function(e) {
               var a, b, i, val = this.value;
               /*close any already open lists of autocompleted values*/
               closeAllLists();
               if (!val) { return false;}
               currentFocus = -1;
               /*create a DIV element that will contain the items (values):*/
               a = document.createElement("DIV");
               a.setAttribute("id", this.id + "autocomplete-list");
               a.setAttribute("class", "autocomplete-items");
               /*append the DIV element as a child of the autocomplete container:*/
               this.parentNode.appendChild(a);
               /*for each item in the array...*/
               for (i = 0; i < arr.length; i++) {
                 /*check if the item starts with the same letters as the text field value:*/
                 if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                   /*create a DIV element for each matching element:*/
                   b = document.createElement("DIV");
                   /*make the matching letters bold:*/
                   b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                   b.innerHTML += arr[i].substr(val.length);
                   /*insert a input field that will hold the current array item's value:*/
                   b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                   /*execute a function when someone clicks on the item value (DIV element):*/
                   b.addEventListener("click", function(e) {
                       /*insert the value for the autocomplete text field:*/
                       inp.value = this.getElementsByTagName("input")[0].value;
         			  //displayUserProfile(inp.value);
					   populateTeams(inp.value);
         			   getDataFromServer(inp.value,null);
					   console.log(inp.value);
                       /*close the list of autocompleted values,
                       (or any other open lists of autocompleted values:*/
                       closeAllLists();
                   });
                   a.appendChild(b);
                 }
               }
           });
           /*execute a function presses a key on the keyboard:*/
           inp.addEventListener("keydown", function(e) {
               var x = document.getElementById(this.id + "autocomplete-list");
               if (x) x = x.getElementsByTagName("div");
               if (e.keyCode == 40) {
                 /*If the arrow DOWN key is pressed,
                 increase the currentFocus variable:*/
                 currentFocus++;
                 /*and and make the current item more visible:*/
                 addActive(x);
               } else if (e.keyCode == 38) { //up
                 /*If the arrow UP key is pressed,
                 decrease the currentFocus variable:*/
                 currentFocus--;
                 /*and and make the current item more visible:*/
                 addActive(x);
               } else if (e.keyCode == 13) {
                 /*If the ENTER key is pressed, prevent the form from being submitted,*/
                 e.preventDefault();
                 if (currentFocus > -1) {
                   /*and simulate a click on the "active" item:*/
                   if (x) x[currentFocus].click();
                 }
               }
           });
           function addActive(x) {
             /*a function to classify an item as "active":*/
             if (!x) return false;
             /*start by removing the "active" class on all items:*/
             removeActive(x);
             if (currentFocus >= x.length) currentFocus = 0;
             if (currentFocus < 0) currentFocus = (x.length - 1);
             /*add class "autocomplete-active":*/
             x[currentFocus].classList.add("autocomplete-active");
           }
           function removeActive(x) {
             /*a function to remove the "active" class from all autocomplete items:*/
             for (var i = 0; i < x.length; i++) {
               x[i].classList.remove("autocomplete-active");
             }
           }
           function closeAllLists(elmnt) {
             /*close all autocomplete lists in the document,
             except the one passed as an argument:*/
             var x = document.getElementsByClassName("autocomplete-items");
             for (var i = 0; i < x.length; i++) {
               if (elmnt != x[i] && elmnt != inp) {
                 x[i].parentNode.removeChild(x[i]);
               }
             }
           }
           
         }
         
         
         window.onload = function() {
         $('.col-lg-12').width();
         	console.log("Initiating...");
            var dataString = 'test';
            $.ajax({
                type:'POST',
                data:dataString,
                url:'get_questions.php',
         	   dataType: 'json',
                success:function(data) {
         		//console.log(data);
         		var questions=[];
		
				for (var i = 0; i < data.length; i++) {
					questions[i]=data[i];
				}
				autocomplete(document.getElementById("myInput"), questions);
				}
         	});
			
			getDataFromServer("Drug Interdiction",null);
         	
         };
         

 function populateTeams(problem)
{
	
	console.log("Problem: "+problem);
	var last_name = 'N/A';
	var dataString = 'myQuestion='+problem;
	
	   $.ajax({
       type:'POST',
       data:dataString,
       url:'get_teams.php',
	   dataType: 'json',
       success:function(data) {
		$('.dropdown-menu').empty();
		dataLength=data.length;
		console.log(data.length);
		for (var i = 0; i < data.length; i++) {
			//var splitData = data[i].split(",");
			//console.log("====>"+data[i][1]);
			var teamName=data[i][1].trim();
			var teamID=data[i][0].trim();
			$('.dropdown-menu').append('<li><a  href="javascript:getDataFromServer(\''+problem+'\',\''+teamName+'\');" ><span class="tab">'+teamName+'</span></a></li>');
        }
		
		}
	});
}

$(function(){

    $("#dmenue").on('click', 'li a', function(){
      $("#dbutton:first-child").text($(this).text());
      $("#dbutton:first-child").val($(this).text());
   });

});
		 
function getDataFromServer(problem,team)
{
	   console.log("Problem: "+problem+" | Team: "+team);
	   
		
	
	   var dataString = 'problem='+problem+"&team="+team;
	   
	   $.ajax({
        type:'POST',
        data:dataString,
		dataType: 'json',
        url:'get_problem_sentiments.php',
        success:function(data) {
			console.log(data);			
			//console.log("JSON from server: "+data);
			displayData(data);
			}
		});

}	
		 
 function displayData(data)
 {
	var chart = $('#container').highcharts();
	
	if(chart)
	{
		chart.destroy();
	}
	
	Highcharts.chart('container', {

  title: {
    text: 'Number of Comments on a Date'
  },

  subtitle: {
    text: 'Source: Swarm\'18'
  },

  yAxis: {
    title: {
      text: 'Number of Comments!'
    }
  },
  
  xAxis: {
    categories: data.dateRange
	},
  
  legend: {
    layout: 'vertical',
    align: 'right',
    verticalAlign: 'middle'
  },

  plotOptions: {
    series: {
      label: {
        connectorAllowed: false
      }
    }
  },

  series: [{
    name: 'Positive Comments',
    data: data.positiveComments
  }, {
    name: 'Negative Comments',
    data: data.negativeComments
  },{
    name: 'Neutral Comments',
    data: data.neutralComments
  },{
    name: 'Total Comments',
    data: data.totalComments
  }],

  responsive: {
    rules: [{
      condition: {
        maxWidth: 500
      },
      chartOptions: {
        legend: {
          layout: 'horizontal',
          align: 'center',
          verticalAlign: 'bottom'
        }
      }
    }]
  }

});
		
}
		
if ($(window).width() < 480 || $(window).height() < 480) {
    //small screen, load other JS files
   console.log("TeSt!");
   $(".playerOne").css("left","30px");
   $(".playerOne").css("position","relative");
   $(".playerTwo").css("right","100px");
   $(".playerTwo").css("top","5px");
   $(".playerTwo").css("position","relative");
}		
		
		 
         
      </script>
   </body>
</html>