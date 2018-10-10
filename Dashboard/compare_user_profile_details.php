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
	  
	  
	  
	  /*AutoComplete Problem*/
	  .autocompleteProblem {
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
      .autocompleteProblem-items {
      position: absolute;
      border: 1px solid #d4d4d4;
      border-bottom: none;
      border-top: none;
      z-index: 99;
      /*position the autocompleteProblem items to be the same width as the container:*/
      top: 100%;
      left: 0;
      right: 0;
      }
      .autocompleteProblem-items div {
      padding: 10px;
      cursor: pointer;
      background-color: #fff; 
      border-bottom: 1px solid #d4d4d4; 
      }
      .autocompleteProblem-items div:hover {
      /*when hovering an item:*/
      background-color: #e9e9e9; 
      }
      .autocompleteProblem-active {
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
   <body >

    
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
                           <input id="myInput" type="text" name="myUser" placeholder="Search User">
                        </div>
                     </div>
		
                  </div>
               </div>
       
               <hr />
			   <div class="row bg-white m-l-0 m-r-0 box-shadow ">
                  <!--<button id="update">Test</button>-->
                  <!-- column -->
                  <div class="col-lg-12">
					<div id="containerUserPosts"></div>
                  </div>
                  <!-- column -->
               </div>

			    <hr />
			   
			    <div class="row">
				<!--<button onclick="getDataFromServer('Drugs!');">Test</button>-->
                  <div class="col-md-12">
				    <div class="playerOne">
                        <span class="input-group-btn"><button class="btn btn-primary" type="submit"><i class="ti-search"></i></button></span>
                        <div class="autocompleteProblem">
                           <input id="myInputProblem" type="text" name="myProblem" placeholder="Search Problem">
					    </div>
                     </div>
                  </div>
               </div>
			    <hr />
			   			  <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
							<div id="containerComments"></div>
                        </div>
                        <!-- /# card -->
                    </div>
                    <!-- /# column -->
					
					<div class="col-lg-4">
                        <div class="card">
							<div id="containerPings"></div>
                        </div>
                        <!-- /# card -->
                    </div>
                    <!-- /# column -->

                    <div class="col-lg-4">
                        <div class="card">
                            <div id="containerSentiments"></div>
                        </div>
                        <!-- /# card -->
                    </div>
                    <!-- /# column -->
                </div>
			   
                <div class="row bg-white m-l-0 m-r-0 box-shadow ">

                  <div class="col-lg-12">
					<div id="containerTimeline"></div>
                  </div>
        
               </div>
			   
			   
               <!-- End PAge Content -->
            </div>
            <!-- End Container fluid  -->
            <!-- footer -->
            <?php include 'footer.php';?>
            <!-- End footer -->
   

      <?php include 'footerScripts.php';?>
      <script src="code/highcharts.js"></script>
      <script src="code/highcharts-3d.js"></script>
      <script src="https://code.highcharts.com/modules/data.js"></script>
      <script>
	  
	  var globalUser=null;
	  
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
         			   // getDataFromServer(inp.value);
					   globalUser=inp.value;
					   getDataFromServerUserPosts(inp.value);
					   populateProblem(inp.value);	   
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
		 
		 function autocompleteProblem(inp, arr) {
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
               a.setAttribute("id", this.id + "autocompleteProblem-list");
               a.setAttribute("class", "autocompleteProblem-items");
               /*append the DIV element as a child of the autocompleteProblem container:*/
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
                       /*insert the value for the autocompleteProblem text field:*/
                       inp.value = this.getElementsByTagName("input")[0].value;
         			   //getDataFromServer(inp.value);
					   //globalUser
					   getDataFromServer(globalUser,inp.value);
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
               var x = document.getElementById(this.id + "autocompleteProblem-list");
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
             /*add class "autocompleteProblem-active":*/
             x[currentFocus].classList.add("autocompleteProblem-active");
           }
           function removeActive(x) {
             /*a function to remove the "active" class from all autocompleteProblem items:*/
             for (var i = 0; i < x.length; i++) {
               x[i].classList.remove("autocompleteProblem-active");
             }
           }
           function closeAllLists(elmnt) {
             /*close all autocompleteProblem lists in the document,
             except the one passed as an argument:*/
             var x = document.getElementsByClassName("autocompleteProblem-items");
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
			
			//getDataFromServer("boobook364");
         	//displayData(null);bilby953
			getDataFromServerUserPosts("bilby953");
			getDataFromServer("bilby953","Drug Interdiction");
         };
         
	

$(function(){

    $("#dmenue").on('click', 'li a', function(){
      $("#dbutton:first-child").text($(this).text());
      $("#dbutton:first-child").val($(this).text());
   });

});
		 

function populateProblem(user)
{
	console.log("user: "+user);
	 
	 var dataString = 'user='+user;
            $.ajax({
                type:'POST',
                data:dataString,
                url:'get_user_problem.php',
         	   dataType: 'json',
                success:function(data) {
         		//console.log(data);
         		var problems=[];
		
				for (var i = 0; i < data.length; i++) {
					problems[i]=data[i];
				}
				autocompleteProblem(document.getElementById("myInputProblem"), problems);
				
				}
         	});
		
	//autocompleteProblem(document.getElementById("myInputProblem"), questions);
	
}	

function getDataFromServer(user,problem)
{
	   console.log("user: "+user+" | problem: "+problem);
	   
	   var dataString = 'user='+user+"&problem="+problem;
	   
	   $.ajax({
        type:'POST',
        data:dataString,
		dataType: 'json',
        url:'get_profile_details.php',
        success:function(data) {
			console.log(data);			
			//console.log("JSON from server: "+data);
			displayData(data);
			}
		});

}	
		 
function displayData(data)
 {
	var chart = $('#containerTimeline').highcharts();
	
	if(chart)
	{
		chart.destroy();
	}

	Highcharts.chart('containerComments', {
  chart: {
    type: 'pie',
    options3d: {
      enabled: true,
      alpha: 45,
      beta: 0
    },
  events: {
        load: function(event) {
            var total = 0; // get total of data
            for (var i = 0, len = this.series[0].yData.length; i < len; i++) {
                total += this.series[0].yData[i];
            }
            var text = this.renderer.text(
                'Total: ' + total+' Posts',
                this.plotLeft,
                this.plotTop + 10
            ).attr({
                zIndex: 5
            }).add() // write it to the upper left hand corner
        }
    }
  },
  title: {
    text: 'Comments/Description/Hypothesis'
  },
  tooltip: {
    pointFormat: '{series.name}: <b>{point.y:.1f}</b>'
  },
  plotOptions: {
    pie: {
      allowPointSelect: true,
      cursor: 'pointer',
      depth: 35,
      dataLabels: {
        enabled: true,
        format: '{point.name}'
      }
    }
  },
  series: [{
    type: 'pie',
    name: 'Posts Distribution',
    data: data.posts
  }],
  credits: {
      enabled: false
  }
});

	Highcharts.chart('containerPings', {
  chart: {
    type: 'pie',
    options3d: {
      enabled: true,
      alpha: 45,
      beta: 0
    }
  },
  title: {
    text: 'Sentiments'
  },
  tooltip: {
    pointFormat: '{series.name}: <b>{point.y:.1f}</b>'
  },
  plotOptions: {
    pie: {
      allowPointSelect: true,
      cursor: 'pointer',
      depth: 35,
      dataLabels: {
        enabled: true,
        format: '{point.name}'
      }
    }
  },
  series: [{
    type: 'pie',
    name: 'Browser share',
    data: data.sentiments
  }]
});

	Highcharts.chart('containerSentiments', {
  chart: {
    type: 'pie',
    options3d: {
      enabled: true,
      alpha: 45,
      beta: 0
    }
  },
  title: {
    text: 'Incoming/Outgoing Pings'
  },
  tooltip: {
    pointFormat: '{series.name}: <b>{point.y:.1f}</b>'
  },
  plotOptions: {
    pie: {
      allowPointSelect: true,
      cursor: 'pointer',
      depth: 35,
      dataLabels: {
        enabled: true,
        format: '{point.name}'
      }
    }
  },
  series: [{
    type: 'pie',
    name: 'Browser share',
    data: data.pings
  }]
});


	
	Highcharts.chart('containerTimeline', {

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
		
		
		
		
		
function getDataFromServerUserPosts(user)
{
	   console.log("user---> "+user);
	   
		
	
	   var dataString = 'user='+user;
	   
	   $.ajax({
        type:'POST',
        data:dataString,
		dataType: 'json',
        url:'get_user_profile.php',
        success:function(data) {
			console.log(data);			
			//console.log("JSON from server: "+data);
			displayDataUserPosts(data);
			}
		});

}	
		 
function displayDataUserPosts(data)
 {
	var chart = $('#containerUserPosts').highcharts();
	
	if(chart)
	{
		chart.destroy();
	}

Highcharts.chart('containerUserPosts', {
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