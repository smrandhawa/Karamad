<?php date_default_timezone_set('Asia/Dubai'); ?>
<?php require('users.php'); ?>
<?php require('stat.php'); ?>
<html>
   <head>
      <title>Statistics of Karamad System</title>
      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js">
      </script>
      <script src = "https://code.highcharts.com/highcharts.js"></script> 
      <link rel="stylesheet" type="text/css" href="style.css"> 
   </head>
   
   <body>
      <hr>
      <center>
      <h1 class="style3">Karamad Real-Time Monitor </h1>
      <!--<h2 class="style3">Generated on <?php echo date("l") . ", " . date("Y-m-d, H:i:s"); ?></h2>-->
      <div id = "container" style = "width: 85%; height: 90%; margin: 0 auto; padding: 10px; outline: 2px solid black;"></div>
      <br>
      <hr>
      <table class="table7">
        <thead>
          <tr>
             <th rowspan="2" style="text-align: center">
                Statistics
             </th>
             <th colspan="2" style="text-align: center">
                <b>Count</b>
             </th>
          </tr>
         </thead>
         <tbody>
             <tr>
                 <td> Number of Calls made by users</td>
                 <td colspan="1" style="text-align:center"><?php echo number_format(Number_of_Calls()); ?></td>
             </tr>
             <tr>
                 <td> Number of Pre-Answered Calls</td>
                 <td colspan="1" style="text-align:center"><?php echo number_format(Number_of_PreAnswer_Calls()); ?></td>
             </tr>
             <tr>
                 <td> Number of Answered Calls</td>
                 <td style="text-align:center"><?php echo number_format(Number_of_Answered_Calls()); ?></td>
             </tr>
             <tr>
                 <td> Number of Users who Called</td>
                 <td style="text-align:center"><?php echo number_format(Number_of_Users()); ?></td>
             </tr>
             <tr>
                 <td> Number of Users who Attempted Surveys</td>
                 <td style="text-align:center"><?php echo number_format(Number_of_Users_who_Attempted_Survey()); ?></td>
             </tr>
             <tr>
                 <td> Total Surveys Attempted</td>
                 <td style="text-align:center"><?php echo number_format(Number_of_Surveys_Attempted()); ?></td>
             </tr>
             <tr>
                 <td> Total Surveys Completed</td>
                 <td style="text-align:center"><?php echo number_format(Number_of_Surveys_Completed()); ?></td>
             </tr>
             <tr>
                 <td> Total Questions Answered</td>
                 <td style="text-align:center"><?php echo number_format(Number_of_Questions_Answered()); ?></td>
             </tr>
             <tr>
                 <td> Total Money Paid (approximately)</td>
                 <td style="text-align:center"><?php echo '$' ?><?php echo round(Total_Money_Paid()/155, 2); ?></td>
             </tr>

         </tbody>
      </table>
      <br>
      <hr>
      <h4 class="style3">Note: The links below are not based on real-time queries/data. They are updated weekly. Data for them is last-calculated on 02-03-2020.</h4>
      <table class="table3">
         <tbody>
            <tr>
               <td style="text-align: center">
                  <b>Overall Analysis Graphs Links</b>
               </td>
            </tr>
            <tr>
               <td style="display:table-cell; vertical-align:top">
                  <a href="https://public.tableau.com/profile/shan7594#!/vizhome/Attempts_of_each_Survey/AttemptsCompletionandEligibility">1. Attempts of each Survey</a>
                  <br>
                  <a href="https://public.tableau.com/profile/shan7594#!/vizhome/KaramadRetention/Retention">2. User Retention</a>
                  <br>
               </td>
            </tr>
         </tbody>
      </table>
      <br>
      <table class="table3">
         <tbody>
            <tr>
               <td style="text-align: center">
                  <b>Survey Wise Analysis Tables Links</b>
               </td>
            </tr>
            <tr>
               <td style="display:table-cell; vertical-align:top">
                  <a href="https://public.tableau.com/profile/shan7594#!/vizhome/BasicDemographicsSurvey/Basic_Demographics_Survey">1. Basic Demographics</a>
                  <br>
                  <a href="https://public.tableau.com/profile/shan7594#!/vizhome/AccessibilitySurvey/Accessibility_Survey">2. Accessibility</a>
                  <br>
                  <a href="https://public.tableau.com/profile/shan7594#!/vizhome/HealthSurvey/Health_Survey">3. Health</a>
                  <br>
                  <a href="https://public.tableau.com/profile/shan7594#!/vizhome/LiteracySurvey_15784800151910/Literacy_Survey">4. Literacy</a>
                  <br>
                  <a href="https://public.tableau.com/profile/shan7594#!/vizhome/DisabilitySurvey/Disability_Survey">5. Disability</a>
                  <br>
                  <a href="https://public.tableau.com/profile/shan7594#!/vizhome/EthnicityLanguageSurvey/EthnicityLanguage_survey">6. Ethnicity/Language</a>
                  <br>
                  <a href="https://public.tableau.com/profile/shan7594#!/vizhome/FormalEducationSurvey/education_survey">7. Formal Education</a>
                  <br>
                  <a href="https://public.tableau.com/profile/shan7594#!/vizhome/EmploymentSurvey/employment_survey">8. Employment</a>
                  <br>
               </td>
            </tr>
         </tbody>
      </table>
      <hr>
      </center>
      <script language = "JavaScript">
         $(document).ready(function() {  
            var chart = {
               backgroundColor: '#FFFFFF',
               zoomType: 'xy',
            };
            var title = {
               text: 'Number of Calls and Users per Day'   
            };
            var xAxis = {
               title: {
                     text: 'Day',
                     style: {
                        color: '#000000',
                        fontWeight: 'normal',
                        fontSize: '16px',
                        fontFamily: 'Verdana'
                     }
                  },
               type: 'datetime',
               crosshair: true,
               tickInterval : 5,
               labels: {
                     rotation: -45,
                     align: 'right',
                     style: {
                        color: '#000000',
                        fontWeight: 'normal',
                        fontSize: '14px',
                        fontFamily: 'Verdana'
                     }
                  },
            };
            var yAxis = [
               { // Primary yAxis
                  labels: {
                     format: '{value}',
                     style: {
                        color: '#000000',
                        fontWeight: 'normal',
                        fontSize: '16px',
                        fontFamily: 'Verdana'
                     }
                  },
                  title: {
                     text: 'Calls',
                     style: {
                        color: '#000000',
                        fontWeight: 'normal',
                        fontSize: '14px',
                        fontFamily: 'Verdana'
                     }
                  },
                  gridLineColor: '#000000',
                  gridLineDashStyle: 'longdash'
                  
               }, 
               { // Secondary yAxis
                  title: {
                     text: 'Users',
                     style: {
                        color: '#ff2400',
                        fontWeight: 'normal',
                        fontSize: '16px',
                        fontFamily: 'Verdana'
                     }
                  },
                  labels: {
                     format: '{value}',
                     style: {
                        color: '#ff2400',
                        fontWeight: 'normal',
                        fontSize: '14px',
                        fontFamily: 'Verdana'
                     }
                  },
                  opposite: true,
                   min: 0,
                   max: 125,
                   gridLineColor: '#000000',
                   gridLineDashStyle: 'longdash'
               }, 
               { // Tertiary yAxis
                  gridLineWidth: 0,
                  title: {
                     text: 'New-Users',
                     style: {
                        color: '#ffffff',
                        fontWeight: 'normal',
                        fontSize: '16px',
                        fontFamily: 'Verdana'
                     }
                  },
                  labels: {
                     format: '{value}',
                     style: {
                        color: '#ffffff',
                        fontWeight: 'normal',
                        fontSize: '14px',
                        fontFamily: 'Verdana'

                     }
                  },
                  opposite:true,
                  min: 0,
                  max: 125
                   
               }
            ];
            var tooltip = {
               shared: true
            };
            var legend = {
               layout: 'vertical',
               align: 'left',
               x: 120,
               verticalAlign: 'top',
               y: 100,
               floating: true,
               
               backgroundColor: (
                  Highcharts.theme && Highcharts.theme.legendBackgroundColor)
                  || '#FFFFFF'
            };

            var plotOptions = {
                   column: {
                        grouping: false//,
                        //shadow: false
                     },
                     series: {
                     groupPadding: 0.0
                  }
               };

            var series = [
               {
                  name: 'Calls',
                  type: 'line',
                  yAxis: 0,
                  marker: {
                     enabled: true
                  },
                  color: '#000000',
                  zIndex: 2
                  /*,
                  tooltip: {
                     valueSuffix: ' mm'
                  }
                  */
               },
               {
                  name: 'Users',
                  type: 'column',
                  yAxis: 1,
                  marker: {
                     enabled: false
                  },
                  color: '#ff2400',
                  zIndex: 1
                  /*
                  tooltip: {
                     valueSuffix: ' mb'
                  }
                  */
               },
               {
                  name: 'New-Users',
                  type: 'column',
                  marker: {
                     enabled: false
                  },
                  yAxis: 2,
                  zIndex: 1
                  /*
                  tooltip: {
                     valueSuffix: '\xB0C'
                  }
                  */
               }
            ];   
            
            jQuery.get('file.txt', null, function(tsv) {
               //alert("in jquery function");
               var lines = [];
               traffic1 = [];
               traffic2 = [];
               traffic3 = [];
               cat = [];
               try {
                  // split the data return into lines and parse them
                  tsv = tsv.split(/<br>/);
                  //alert(tsv);

                  jQuery.each(tsv, function(i, line) {
                     line = line.split(/\t/);
                     date = '';
                     if(i == 0)
                     {
                        date = line[0].split(/\n/)[1];
                     }
                     else
                     {
                        date = line[0];
                     }

                     //alert(line);
                     traffic1.push([
                        date,
                        parseInt(line[1])
                     ]);
                     traffic2.push([
                        date,
                        parseInt(line[2])
                     ]);

                     traffic3.push([
                        date,
                        parseInt(line[3])
                     ]);
                     
                     cat.push([
                        date
                     ]);
                  });
               } catch (e) {  }
               
               //alert(cat);
               series[0].data = traffic3;
               series[1].data = traffic1;
               series[2].data = traffic2;

               var json = {};   
               json.chart = chart;   
               json.title = title;
               xAxis.categories = cat;
               json.plotOptions = plotOptions;
               json.xAxis = xAxis;
               json.yAxis = yAxis;
               json.tooltip = tooltip;  
               json.legend = legend;  
               json.series = series;
               $('#container').highcharts(json);
            });   
         });
      </script>
      
      
   </body>
   
</html>