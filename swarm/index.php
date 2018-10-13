<!DOCTYPE html>
<html lang="en">

<?php include 'header.php';?>

<style type="text/css">
        .node {
			
		}
        .link { 
		stroke: #999; 
		stroke-opacity: .6; 
		}
		
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
	
.playerOne {
float: left;
}
.playerTwo {
float: right;
}
	
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
                    <h3 class="text-primary">Basics</h3> </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Interactions</li>
                        <li class="breadcrumb-item active">User Tags</li>
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
				<!--<button id="update">Test</button>-->
                    <!-- column -->
                    <div class="col-lg-12">
						<svg id="myGraph" width="800" height="500"></svg>     
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

	</body>
</html>


<script src="http://d3js.org/d3.v4.min.js" type="text/javascript"></script>
<script src="http://d3js.org/d3-selection-multi.v1.js"></script>

<script type="text/javascript">
	
	window.onload = function() {

	};
	
	function testButton()
	{
		//svg.selectAll("*").remove();
	   var dataString = 'test';
	   $.ajax({
        type:'POST',
        data:dataString,
        url:'getdata.php',
        success:function(data) {
			console.log(data);
			var obj1 = JSON.stringify(data);
			var obj2 = JSON.parse(data);	
			//draw_graph(data);			
			console.log(obj2.links);
			d3.select('#myGraph').html("");
			updateOnClick(obj2.links,obj2.nodes);
			}
		});
	}
	
	
	function getInteraction(teamName,myQuestion)
	{
		console.log(teamName+"===="+myQuestion);
		//svg.selectAll("*").remove();
	   var dataString = 'myQuestion='+myQuestion+'&teamName='+teamName;
	   $.ajax({
        type:'POST',
        data:dataString,
        url:'get_team_interaction.php',
        success:function(data) {
			console.log(data);
			var obj1 = JSON.stringify(data);
			var obj2 = JSON.parse(data);	
			//draw_graph(data);			
			//console.log(obj2.links);
			d3.select('#myGraph').html("");
			updateOnClick(obj2.links,obj2.nodes);
			}
		});
	}
	
	function updateOnClick(links,nodes)
	{
		d3.scaleOrdinal(d3.schemeCategory10);
		
		svg = d3.select("svg"),
        width = +svg.attr("width"),
        height = +svg.attr("height"),
        node,
        link;
		
		svg.append('defs').append('marker')
        .attrs({'id':'arrowhead',
            'viewBox':'-0 -5 10 10',
            'refX':13,
            'refY':0,
            'orient':'auto',
            'markerWidth':13,
            'markerHeight':13,
            'xoverflow':'visible'})
        .append('svg:path')
        .attr('d', 'M 0,-5 L 10 ,0 L 0,5')
        .attr('fill', '#999')
        .style('stroke','none');
		
		simulation = d3.forceSimulation()
      .force("link", d3.forceLink().id(function (d) {return d.id;}).distance(200).strength(1))
        .force("charge", d3.forceManyBody())
        //.force("center", d3.forceCenter(width/2, height/2))
		.velocityDecay(0.1)
		.force("x", d3.forceX(width / 2).strength(.05))
		.force("y", d3.forceY(height / 2).strength(.05))
		.force("charge", d3.forceManyBody().strength(-50))
		;
		
		    link = svg.selectAll(".link")
            .data(links)
            .enter()
            .append("line")
			.attr("stroke-width", function(d){ return 1; })
            .attr("class", "link")
            .attr('marker-end','url(#arrowhead)')

        link.append("title")
            .text(function (d) {return d.type;});

        edgepaths = svg.selectAll(".edgepath")
            .data(links)
            .enter()
            .append('path')
            .attrs({
                'class': 'edgepath',
                'fill-opacity': 0,
                'stroke-opacity': 0,
                'id': function (d, i) {return 'edgepath' + i}
            })
            .style("pointer-events", "none");

        node = svg.selectAll(".node")
            .data(nodes)
            .enter()
            .append("g")
            .attr("class", "node")
            .call(d3.drag()
                    .on("start", dragstarted)
                    .on("drag", dragged)
                    .on("end", dragended)
            );

        node.append("circle")
            .attr("r", function(d){ 
			var weight;
			return d.weight*3;
			})
            .style("fill", function (d, i) {return colors(i);})

        node.append("title")
            .text(function (d) {return d.id;});

        node.append("text")
            .attr("dy", -3)
            .text(function (d) {
				return d.name;
				});

        simulation
            .nodes(nodes)
            .on("tick", ticked);

        simulation.force("link")
            .links(links);
	}


    var colors = d3.scaleOrdinal(d3.schemeCategory10);

    var svg = d3.select("svg"),
        width = +svg.attr("width"),
        height = +svg.attr("height"),
        node,
        link;

    svg.append('defs').append('marker')
        .attrs({'id':'arrowhead',
            'viewBox':'-0 -5 10 10',
            'refX':13,
            'refY':0,
            'orient':'auto',
            'markerWidth':13,
            'markerHeight':13,
            'xoverflow':'visible'})
        .append('svg:path')
        .attr('d', 'M 0,-5 L 10 ,0 L 0,5')
        .attr('fill', '#999')
        .style('stroke','none');

    var simulation = d3.forceSimulation()
		.force("link", d3.forceLink().id(function (d) {return d.id;}).distance(200).strength(1))
        .force("charge", d3.forceManyBody())
        //.force("center", d3.forceCenter(width/2, height/2))
		.velocityDecay(0.1)
		.force("x", d3.forceX(width / 2).strength(.05))
		.force("y", d3.forceY(height / 2).strength(.05))
		.force("charge", d3.forceManyBody().strength(-50))
		;


    function update(links, nodes) {
        link = svg.selectAll(".link")
            .data(links)
            .enter()
            .append("line")
			.attr("stroke-width", function(d){ return 1; })
            .attr("class", "link")
            .attr('marker-end','url(#arrowhead)')

        link.append("title")
            .text(function (d) {return d.type;});

        edgepaths = svg.selectAll(".edgepath")
            .data(links)
            .enter()
            .append('path')
            .attrs({
                'class': 'edgepath',
                'fill-opacity': 0,
                'stroke-opacity': 0,
                'id': function (d, i) {return 'edgepath' + i}
            })
            .style("pointer-events", "none");

        node = svg.selectAll(".node")
            .data(nodes)
            .enter()
            .append("g")
            .attr("class", "node")
            .call(d3.drag()
                    .on("start", dragstarted)
                    .on("drag", dragged)
                    .on("end", dragended)
            );

        node.append("circle")
            .attr("r", function(d){ 
			var weight;
			return d.weight*3;
			})
            .style("fill", function (d, i) {return colors(i);})

        node.append("title")
            .text(function (d) {return d.id;});

        node.append("text")
            .attr("dy", -3)
            .text(function (d) {
				return d.name;
				});

        simulation
            .nodes(nodes)
            .on("tick", ticked);

        simulation.force("link")
            .links(links);
    }

    function ticked() {
        link
            .attr("x1", function (d) {return d.source.x;})
            .attr("y1", function (d) {return d.source.y;})
            .attr("x2", function (d) {return d.target.x;})
            .attr("y2", function (d) {return d.target.y;});

        node
            .attr("transform", function (d) {return "translate(" + d.x + ", " + d.y + ")";});

        edgepaths.attr('d', function (d) {
            return 'M ' + d.source.x + ' ' + d.source.y + ' L ' + d.target.x + ' ' + d.target.y;
        });

    }

    function dragstarted(d) {
        if (!d3.event.active) simulation.alphaTarget(0.3).restart()
        d.fx = d.x;
        d.fy = d.y;
    }

    function dragged(d) {
        d.fx = d3.event.x;
        d.fy = d3.event.y;
    }
	
	function dragended(d) {
		if (!d3.event.active) simulation.alphaTarget(0);
		d.fx = undefined;
		d.fy = undefined;
	}


$("#update").click(function(e) {
	testButton();
});


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
			  
		    var problem = inp.value;
			var dataString = 'problem='+problem;
			
			   $.ajax({
				type:'POST',
				data:dataString,
				url:'get_tags.php',
				success:function(data) {
					console.log(data);
					var obj1 = JSON.stringify(data);
					var obj2 = JSON.parse(data);			
					//console.log(obj2.links);
							d3.select('#myGraph').html("");
			updateOnClick(obj2.links,obj2.nodes);
					}
				});
			  
			  populateTeams(inp.value);
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


function populateTeams(myQuestion)
{
	
	console.log("Check-1");
	var last_name = 'N/A';
	var dataString = 'myQuestion='+myQuestion+'&last_name='+last_name;
	
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
			$('.dropdown-menu').append('<li><a  href="javascript:getInteraction(\''+teamName+'\',\''+myQuestion+'\');" ><span class="tab">'+teamName+'</span></a></li>');
        }
		
		}
	});
}

window.onload = function() {
   var dataString = 'test';
   $.ajax({
       type:'POST',
       data:dataString,
       url:'get_questions.php',
	   dataType: 'json',
       success:function(data) {
		
		var questions=[];
		
		for (var i = 0; i < data.length; i++) {
            questions[i]=data[i];
        }
		console.log(questions);
		autocomplete(document.getElementById("myInput"), questions);
		//var questions=[data];
		//questions = data;
		//printQuestions(questions);
		}
	});
	
	var problem = 'Drug Interdiction';
	var dataString = 'problem='+problem;
	
	   $.ajax({
        type:'POST',
        data:dataString,
        url:'get_tags.php',
        success:function(data) {
			console.log(data);
			var obj1 = JSON.stringify(data);
			var obj2 = JSON.parse(data);			
			//console.log(obj2.links);
			update(obj2.links,obj2.nodes);
			}
		});
	
};


function printQuestions(questions)
{
	console.log(questions);
	var qst=questions;
	console.log("----)"+qst.length)
	autocomplete(document.getElementById("myInput"), qst);
}

/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
//autocomplete(document.getElementById("myInput"), questions);

</script>