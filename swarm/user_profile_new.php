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
                     <li class="breadcrumb-item active">User Profile</li>
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
                           <input id="myInput" type="text" name="myProblem" placeholder="Search User">
                        </div>
                     </div>
                  </div>
               </div>
       
               <hr />
               <div class="row bg-white m-l-0 m-r-0 box-shadow ">
                  <!--<button id="update">Test</button>-->
                  <!-- column -->
                  <div class="col-lg-12">
					<div id="container"></div>
                  </div>
                  <!-- column -->
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
         			   getDataFromServer(inp.value);
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
                url:'get_users.php',
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
			
			getDataFromServer("currawong296");
         	
         };
         
	

$(function(){

    $("#dmenue").on('click', 'li a', function(){
      $("#dbutton:first-child").text($(this).text());
      $("#dbutton:first-child").val($(this).text());
   });

});
		 
function getDataFromServer(user)
{
	   console.log("user: "+user);
	   
		
	
	   var dataString = 'user='+user;
	   
	   $.ajax({
        type:'POST',
        data:dataString,
		dataType: 'json',
        url:'get_user_profile.php',
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
  chart: {
    type: 'column'
  },
  title: {
    text: 'User Profile'
  },
  subtitle: {
    text: 'Source: Swarm'
  },
  xAxis: {
    categories: data.problems,
    crosshair: true
  },
  yAxis: {
    min: 0,
    title: {
      text: 'Number of Posts'
    }
  },
  tooltip: {
    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
      '<td style="padding:0"><b> {point.y}</b></td></tr>',
    footerFormat: '</table>',
    shared: true,
    useHTML: true
  },
  plotOptions: {
    column: {
      pointPadding: 0.2,
      borderWidth: 0
    }
  },
  series: [{
    name: 'Hyp',
    data: data.hyp

  }, {
    name: 'Comm',
    data: data.comm

  }, {
    name: 'Desc',
    data: data.desc

  },{
    name: 'Total Posts',
    data: data.total

  }]
});	

}
		
		
		
		 
         
      </script>
   </body>
</html>