/*************************************************************************************************************************************
*
*	88      a8P   ad88888ba   88888888ba,    
*	88    ,88'   d8"     "8b  88      `"8b   
*	88  ,88"     Y8,          88        `8b  
*	88,d88'      `Y8aaaaa,    88         88  
*	8888"88,       `"""""8b,  88         88  
*	88P   Y8b            `8b  88         8P  
*	88     "88,  Y8a     a8P  88      .a8P   
*	88       Y8b  "Y88888P"   88888888Y"'    
*
* 	wes Version 1.0 Copyright 2010 Karl Steltenpohl Development LLC. All Rights Reserved.
*	
*	Licensed under a Commercial Wes License (a "Wes License");
*	either the Wes Forever License (the "Forever License"),
*	or the Wes Annual Licencse (the "Annual License");
*	you may not use this file exept in compliance
*	with at least one Wes License.
*
*	You may obtain a copy of the Wes Licenses at	
*	http://www.wescms.com/license
*
*************************************************************************************************************************************/

var myService;
var myService1;
var myService2;
var myService3;
var dataTable;
var sourceTable;
var sourceChart;
var sourceColumnChart;
var matchedTable;
var notMatchedTable;
var pieChart;
var dataViewMatched;
var dataViewNotMatched;
var requestCount;
var totalSources;
var mainChartData = {};
var motionChart;


var scope = 'https://www.google.com/analytics/feeds';

// Load the Google data JavaScript client library
google.load('gdata', '2.x', {packages: ['analytics']});

// Load the Google data JavaScript client library
google.load('gdata', '1.x');

// Load the Google data JavaScript client library
google.load('gdata', '1.x', {packages: ['analytics']});

// Load the Google Visualization API client Libraries
google.load('visualization', '1', {packages: ['piechart', 'table', 'columnchart']});

// Load the Google Visualization API client Libraries
google.load('visualization', '1', {packages: ['motionChart']});


// Set the callback function when the library is ready
google.setOnLoadCallback(init);

/**
 * Initialize the login controls
 */
function init() {
  myService = 
	  new google.gdata.analytics.AnalyticsService('gaExportAPI_acctSample_v2.0');
  
  myService1 =
      new google.gdata.analytics.AnalyticsService('gaExportAPI_gViz_v1.0');
  
  myService2 =
      new google.gdata.analytics.AnalyticsService('gaExportAPI_gVizBrand_v1.0');
	  
  myService3 =
      new google.gdata.analytics.AnalyticsService('gaExportAPI_motionChart_v1.0');

  sourceTable =
      new google.visualization.Table(document.getElementById('sourceTableDiv'));
	  
  sourceChart =
      new google.visualization.PieChart(document.getElementById('sourceChartDiv'));
	  
  sourceColumnChart =
      new google.visualization.ColumnChart(document.getElementById('sourceColumnChartDiv'));

  matchedTable =
      new google.visualization.Table(document.getElementById('matchedTableDiv'));
	  
  notMatchedTable =
      new google.visualization.Table(document.getElementById('notMatchedTableDiv'));
	  
  pieChart =
      new google.visualization.PieChart(document.getElementById('pieChartDiv'));

  //setupEnterHandler(); 
	  
  getStatus();
  
}

//
// ENTER HANDLER FOR 
//
/**
 * Utility function to setup the event handling for pressing enter
 *     on the text matching input.
 
function setupEnterHandler() {
  document.getElementById('filterInput').onkeydown = function(e) {
    if (e.which == 13) {
      drawViz();
    }
  };
}
*/

// AuthSub Authentication
/**
 * Allow user to grant this script access to their GA data.
 */
function login() {
  google.accounts.user.login(scope);
  getStatus();
}

/**
 * Allow user to remove this script's access to their GA data.
 */
function logout() {
  google.accounts.user.logout();
  getStatus();
}

/**
 * Utility function to setup the login/logout functionality.
 */
function getStatus() {
  var status = document.getElementById('status');
  var loginButton = document.getElementById('loginButton');
  var allAccountsDiv = document.getElementById('allAccountsDiv');
  if (!google.accounts.user.checkLogin(scope)) {
    loginButton.value = 'Connect';
    loginButton.onclick = login;
    status.innerHTML = 'Connect Wes to Google Analytics';
	//allAccountsDiv.innerHTML = '<img src="modules/analytics/images/analytics-steps.jpg">';
  } else {
    loginButton.value = 'Disconnect';
    loginButton.onclick = logout;
    status.innerHTML = 'Connected to Google Analytics';
	allAccountsDiv.innerHTML = '';
	getAccountFeed();
  }
}

//
// GET ACCOUNTS
//

/**
 * Construct a request for the Account Feed and send to the GA Export API.
 */
function getAccountFeed() {
  var myFeedUri = scope + '/accounts/default';

  // Send request to the Analytics API and wait for the results to come back.
  myService.getAccountFeed(myFeedUri, handleMyFeed, handleError);
}

/**
 * Handle and display any error that occurs from the API request.
 * @param {Object} e The error object returned by the Analytics API.
 */
function handleError(e) {
  var error = 'There was an error!\n';
  if (e.cause) {
    error += e.cause.status;
  } else {
    error.message;
  }
  alert(error);
}

/**
 * Outputs the important data returned from the API to the screen.
 * @param {object} myResultsFeedRoot Parameter passed back from the
 *     feed handler.
 */
function handleMyFeed(myResultsFeedRoot) {
  var feed = myResultsFeedRoot.feed;

  // Print top-level information about the feed.
  //document.getElementById('topLevelDiv').innerHTML = getFeedDetails(feed);

  // Print the advanced segment data.
  //document.getElementById('segmentDiv').innerHTML = getAdvancedSegments(feed);

  // Print the custom variable data for one profile.
  //document.getElementById('customVarDiv').innerHTML =
  //    getCustomVarForOneEntry(feed);

  // Print the goal data for one profile.
  //document.getElementById('goalsDiv').innerHTML = getGoalsForOneEntry(feed);

  // Print all the account, web property and profile data.
  document.getElementById('allAccountsDiv').innerHTML = getAllAccountData(feed);
}

/**
 * Returns a string with all the important top level feed information.
 * @param {object} feed The account feed object.
 * @return {string} The imporant data in the top level of the feed.
 */
function getFeedDetails(feed) {
  var output = ['<h4>Important Feed Data</h4>'];
  output.push(
      '<pre>',
      '\nFeed Title        = ', feed.getTitle().getText(),
      '\nReturned Results  = ', feed.getTotalResults().getValue(),
      '\nStart Index       = ', feed.getStartIndex().getValue(),
      '\nItems Per Page    = ', feed.getItemsPerPage().getValue(),
      '\nFeed ID           = ', feed.getId().getValue(),
      '</pre>');

  return output.join('');
}

/**
 * Returns a string will all the advanced segments for the current user.
 * @param {object} feed The account feed object.
 * @return {string} A string with all the segment data.
 */
function getAdvancedSegments(feed) {
  var output = ['<h4>Advanced Segments</h4>'];

  var segments = feed.getSegments();
  if (segments.length == 0) {
    output.push('<p>No advanced segments found</p>');
  } else {
    output.push('<pre>');
    for (var i = 0, segment; segment = segments[i]; ++i) {
      output.push(
          '\nSegment Name       = ', segment.getName(),
          '\nSegment Id         = ', segment.getId(),
          '\nSegment Definition = ', segment.getDefinition().getValue(), '\n');
    }
    output.push('</pre>');
  }

  return output.join('');
}

/**
 * Returns a string with all the custom variable information for the first
 * profile that has csutom variables configured.
 * @param {object} feed The account feed object.
 * @return {string} A string with all the custom variable data.
 */
function getCustomVarForOneEntry(feed) {
  var output = ['<h4>Custom Variables</h4>'];

  var entries = feed.getEntries();
  if (entries.length == 0) {
    output.push('<p>No entries found.</p>');
  } else {
    // Go through each entry to see if any has a custom variable defined.
    for (var i = 0, entry; entry = entries[i]; ++i) {
      var customVars = entry.getCustomVariables();
      if (customVars.length != 0) {
        // Go through all the custom variables for this entry.
        output.push('<pre>');
        for (var j = 0, customVar; customVar = customVars[j]; ++j) {
          output.push(
              '\nCustom Variable Index = ', customVar.getIndex(),
              '\nCustom Variable Name  = ', customVar.getName(),
              '\nCustom variable Scope = ', customVar.getScope(), '\n');
        }
        output.push('</pre>');
        return output.join('');
      }
    }
    output.push('<p>No custom variables defined for this user.</p>');
  }
  return output.join('');
}

/**
 * Returns a string will all the goal information for one profile.
 * @param {object} feed The account feed object.
 * @return {string} A string with all the goal data for one profile.
 */
function getGoalsForOneEntry(feed) {
  var output = ['<h4>Goal Configuration</h4>'];
  var entries = feed.getEntries();

  if (entries.length == 0) {
    output.push('<p>No entries found</p>');
  } else {
    // Go through each entry to see if any have Goal information.
    for (var i = 0, entry; entry = entries[i]; ++i) {
      var goals = entry.getGoals();
      if (goals.length != 0) {
        // Go through all the goals for this entry.
        output.push('<pre>');
        for (var j = 0, goal; goal = goals[j]; ++j) {
          output.push('\n<h3>Goal</h3>');
          output.push(
              '\nGoal Number = ', goal.getNumber(),
              '\nGoal name   = ', goal.getName(),
              '\nGoal Value  = ', goal.getValue(),
              '\nGoal Active = ', goal.getActive(), '\n');

          if (goal.getDestination()) {
            setDestinationGoalData(goal.getDestination(), output);
          } else if (goal.getEngagement()) {
            setEngagementGoalData(goal.getEngagement(), output);
          }
        }
        output.push('</pre>');
        return output.join('');
      }
    }
  }
  return output.join('');
}

/**
 * Adds the important information for destination goals including all the
 * configured steps if they exist, to the output parameter.
 * @param {object} destination The destination object for a goal.
 * @param {string} output The output object to append the destination data.
 */
function setDestinationGoalData(destination, output) {
  output.push('\n\t<h3>Destination Goal</h3>');
  output.push(
      '\n\tExpression      = ', destination.getExpression(),
      '\n\tMatch Type      = ', destination.getMatchType(),
      '\n\tStep 1 Required = ', destination.getStep1Required(),
      '\n\tCase Sensitive  = ', destination.getCaseSensitive(), '\n');

  // Print goal steps.
  var steps = destination.getSteps();
  if (steps.length != 0) {
    output.push('\n\t<h3>Destination Goal Steps</h3>');
    for (var i = 0, step; step = steps[i]; ++i) {
      output.push(
          '\n\tStep Number = ', step.getNumber(),
          '\n\tStep Name   = ', step.getName(),
          '\n\tStep Path   = ', step.getPath(), '\n');
    }
  }
}

/**
 * Adds the important information for engagement goals to the output parameter.
 * @param {object} engagement The engagement object for a goal.
 * @param {string} output The output object to append the destination data.
 */
function setEngagementGoalData(engagement, output) {
  output.push('\n\t<h3>Engagement Goal</h3>');
  output.push(
    '\n\tGoal Type       = ', engagement.getType(),
    '\n\tGoal Comparison = ', engagement.getComparison(),
    '\n\tGoal Threshold  = ', engagement.getThresholdValue(), '\n');
}

/**
 * Returns an HTML table with all the accounts, web properties and profile data
 * the current user has access to. The Table ID values can be used in the ids
 * value of the data feed.
 * @param {object} feed The account feed object.
 * @return {string} An HTML table with all the important account data.
 */
function getAllAccountData(feed) {
  var accountData = ['<form method="POST" ><table width="100%">'];
  var entries = feed.getEntries();

  // Check if no entries returned.
  if (entries.length == 0) {
    accountData.push('This user has access to 0 accounts</table>');
    return accountData.join('');
  }

  // Print table headers.
  var row = [
    //WebPropertyId',
    //'AccountName',
    //'AccountId',
    //'Select',
	'Profile Name',
    'ProfileId'
    //'Table Id',
    //'Currency',
    //'Timezone',
    //'Has Custom Var',
    //'Has Goals'
  ].join('</th><th style="text-align:left;">');
  accountData.push('<tr><th style="text-align:left;">', row, '</th></tr>');

  // Fill the table with data.
  for (var i = 0, entry; entry = entries[i]; ++i) {
    row = [
      //entry.getPropertyValue('ga:webPropertyId'),
      //entry.getPropertyValue('ga:AccountName'),
      //entry.getPropertyValue('ga:AccountId'),
      '<input type="radio" onClick="setProfile(\''+entry.getPropertyValue('ga:ProfileId')+'\',\''+entry.getTitle().getText()+'\');" name="account" value="'+ entry.getPropertyValue('ga:ProfileId')+'"> '+entry.getTitle().getText()+'',
	  //entry.getTitle().getText(),
      entry.getPropertyValue('ga:ProfileId')
      //entry.getTableId().getValue(),
      //entry.getPropertyValue('ga:currency'),
      //entry.getPropertyValue('ga:timezone'),
      //entry.getCustomVariables().length != 0 ? 'Yes' : '',
      //entry.getGoals().length != 0 ? 'Yes' : ''
    ].join('</td><td>');
    accountData.push('<tr><td>', row, '</td></tr>');
  }
  accountData.push('</table>');
  
  accountData.push('<div id="submit">&nbsp;</div>');
		
		
  accountData.push('</form>');
  return accountData.join('');
}

//
// GET PAGES
//

/**
 * Request data from GA Export API
 * WES ANALYTICS PAGES
 */
function getDataFeed(profile) {

  var startdate = document.getElementById('startDatePages').value;
  var enddate = document.getElementById('endDatePages').value;

  var myFeedUri = scope + '/data' +
    '?start-date=' + convertDateToYMD(startdate) + '' +
    '&end-date=' + convertDateToYMD(enddate) + '' +
    '&dimensions=ga:pageTitle,ga:pagePath' +
    '&metrics=ga:pageviews' +
    '&sort=-ga:pageviews' +
    '&max-results=10' +
    '&ids=ga:' + profile;

  myService1.getDataFeed(myFeedUri, handleMyDataFeed, handleError);
}

/**
 * Handle and display any error that occurs from the API request.
 * @param {Object} e The error object returned by the Analytics API.
 */
function handleError(e) {
  var msg = e.cause ? e.cause.statusText : e.message;
  msg = 'ERROR: ' + msg;
  alert(msg);
}

/**
 * Handle all the data returned by GA Export API.
 * Delete existing GViz dataTable before creating a new one.
 * @param {Object} myResultsFeedRoot the feed object
 *     retuned by the data feed.
 */
function handleMyDataFeed(myResultsFeedRoot) {
  dataTable = new google.visualization.DataTable();
  fillDataTable(dataTable, myResultsFeedRoot);
  sourceTable.draw(dataTable);

  // remove the URI column to only graph 1 dimension
  dataTable.removeColumn(0);
  sourceChart.draw(dataTable, {width: 982, height: 200, is3D: true});
  sourceColumnChart.draw(dataTable, {width: 982, height: 200, is3D: true, title: 'Website Performance'});
}

/**
 * Put the feed result into a GViz Data Table.
 * @param {Object} dataTable the GViz dataTable object to put data into.
 * @param {Object} myResultsFeedRoot the feed returned by the GA Export API.
 * @return {Objcet} GViz DataTable object.
 */
function fillDataTable(dataTable, myResultsFeedRoot) {
  var entries = myResultsFeedRoot.feed.getEntries();

  dataTable.addColumn('string', 'Page Title');
  dataTable.addColumn('string', 'Page Uri Path');
  dataTable.addColumn('number', 'Pageviews');

  if (entries.length == 0) {
    dataTable.addRows(1);
    dataTable.setCell(0, 0, 'No Data');
    dataTable.setCell(0, 1, 0);
  } else {
    dataTable.addRows(entries.length);
    for (var idx = 0; idx < entries.length; idx++) {
      var entry = entries[idx];
      var title = entry.getValueOf('ga:pageTitle');
      var keyword = entry.getValueOf('ga:pagePath');
      var visits = entry.getValueOf('ga:pageviews');
      dataTable.setCell(idx, 0, title);
      dataTable.setCell(idx, 1, keyword);
      dataTable.setCell(idx, 2, visits);
    }
  }
}

//
// GET KEYWORDS
//

/**
 * Request data from GA Export API
 */
function getDataFeedK(profile) {

  var startdate = document.getElementById('startDateKeywords').value;
  var enddate = document.getElementById('endDateKeywords').value;

  var myFeedUri = scope + '/data' +
    '?start-date=' + convertDateToYMD(startdate) + '' +
    '&end-date=' + convertDateToYMD(enddate) + '' +
    '&dimensions=ga:keyword' +
    '&metrics=ga:visits' +
    '&sort=-ga:visits' +
    '&max-results=50' +
    '&ids=ga:' + profile;
  myService2.getDataFeed(myFeedUri, handleMyDataFeedK, handleErrorK);
}

/**
 * Handle and display any error that occurs from the API request.
 * @param {Object} e The error object returned by the Analytics API.
 */
function handleErrorK(e) {
  var error = 'There was an error!\n';
  if (e.cause) {
    error += e.cause.statusText;
  } else {
    error += e.message;
  }
  alert(error);
}

/**
 * Handle all the data returned by GA Export API.
 * Create GViz dataTable and dataViews objects.
 * @param {Object} myResultsFeedRoot the feed object
 *     retuned by the data feed.
 */
function handleMyDataFeedK(myResultsFeedRootK) {
  dataTableK = new google.visualization.DataTable();
  fillDataTableK(dataTableK, myResultsFeedRootK);
  dataViewMatched = new google.visualization.DataView(dataTableK);
  dataViewNotMatched = new google.visualization.DataView(dataTableK);

  drawViz();
}

/**
 * Put the feed result into a GViz Data Table.
 * @param {Object} dataTable the GViz dataTable object to put data into.
 * @param {Object} myResultsFeedRoot the feed returned by the GA Export API.
 */
function fillDataTableK(dataTableK, myResultsFeedRootK) {
  var entries = myResultsFeedRootK.feed.getEntries();

  dataTableK.addColumn('string', 'Keyword');
  dataTableK.addColumn('number', 'Visits');

  if (entries.length == 0) {
    dataTableK.addRows(1);
    dataTableK.setCell(0, 0, 'No Data');
    dataTableK.setCell(0, 1, 0);
  } else {
    dataTableK.addRows(entries.length);
    for (var idx = 0; idx < entries.length; idx++) {
      var entry = entries[idx];
      var keyword = entry.getValueOf('ga:keyword');
      var visits = entry.getValueOf('ga:visits');
      dataTableK.setCell(idx, 0, keyword);
      dataTableK.setCell(idx, 1, visits);
    }
  }
}

/**
 * Handle Visualization of data by drawing all the charts on the page.
 * Put focus on the matching form when done.
 */
function drawViz() {
  var matchedRows = [];
  var notMatchedRows = [];

  var matchedVisits = 0;
  var notMatchedVisits = 0;

  // put indicies of matched and not matched rows into two arrays
  // get matched and unmatched total visits
  for (var idx = 0; idx < dataTableK.getNumberOfRows(); idx++) {
    var keyword = dataTableK.getValue(idx, 0);
    var visits = dataTableK.getValue(idx, 1);

    if (keyword.indexOf('(not set)') == -1) {
      if (isMatchedKeyword(keyword)) {
        matchedRows.push(idx);
        matchedVisits += visits;
      } else {
        notMatchedRows.push(idx);
        notMatchedVisits += visits;
      }
    }
  }

  // draw matched table views
  drawTable(dataViewMatched, matchedRows, matchedTable);
  drawTable(dataViewNotMatched, notMatchedRows, notMatchedTable);


  // draw visits pie chart
  drawChart('Visits of Matched', matchedVisits, notMatchedVisits, pieChart);


  //update the table headers
  document.getElementById('matchedVisitsSpan').innerHTML = matchedVisits;
  document.getElementById('notMatchedVisitsSpan').innerHTML = notMatchedVisits;

  document.getElementById('vizDiv').style.visibility = 'visible';
  document.getElementById('filterInput').focus();
}

/**
 * Draw a filtered GViz Table from Givz DataTable.
 * @param {Object} dataView a GViz DataView object.
 * @param {Array} filteredRows an array holding all the row indicies to show.
 * @param {Object} tableObject the visualization Table Object to display.
 */
function drawTable(dataView, filteredRows, tableObject) {
  dataView.setRows(filteredRows);
  tableObject.draw(dataView, null);
}

/**
 * Draw a pie chart from values passed into this function.
 * @param {String} myTitle the title of this chart.
 * @param {Number} chartValueOne the first value to display.
 * @param {Number} chartValueTwo the second number to display.
 * @param {Object} chartObject the visualization Chart Object to display.
 */
function drawChart(myTitle, chartValueOne, chartValueTwo, chartObject) {
  var label1 = 'Matched';
  var label2 = 'Didn\'t Match';

  // create a GViz DataTable JSON data object.
  chartDataTable = new google.visualization.DataTable({
    cols: [
      {id: 'A', label: 'Type', type: 'string'},
      {id: 'B', label: 'Count of Matched', type: 'number'}],
    rows: [
      {c: [{v: label1}, {v: chartValueOne}]},
      {c: [{v: label2}, {v: chartValueTwo}]}
    ]}, 0.5);

  chartObject.draw(chartDataTable, {width: 982, height: 200, title: myTitle, is3D: true});
}

/**
 * Test if the keyword is matched by the filter using a regualr expression match
 * @param {String} keyword the keyword returned to test.
 * @return {Boolean} true or false.
 */
function isMatchedKeyword(keyword) {
  var filter = document.getElementById('filterInput').value;
  return keyword.match(filter);
}

//
// SET PROFILE
//

/**
 * WES FUNCTION
 */
function setProfile(profile,name){
	var aprofile = document.getElementById('aprofile');
	var aname = document.getElementById('aname');
	aprofile.innerHTML = profile;
	aname.innerHTML = name;
	
	document.getElementById('cstatus1').innerHTML = "";
	document.getElementById('cstatus2').innerHTML = "";
	document.getElementById('cstatus3').innerHTML = "";
	
	getDataFeed(profile);
	getDataFeedK(profile);
	getDataFeedM(profile);
}

//
// MOTION CHARTS
//

/**
 * Request data from GA Export API.
 */
function getDataFeedM(profile) {
  showLoadingMessage();

  var chartyear = document.getElementById('yearChart').value;

  var myFeedUri = scope + '/data' +
    '?start-date=' + chartyear + '-01-01' +
    '&end-date=' + chartyear + '-12-31' +
    '&dimensions=ga:source' +
    '&metrics=ga:visits' +
    '&sort=-ga:visits' +
    '&max-results=7' +
    '&ids=ga:' + profile;
    
  myService3.getDataFeed(myFeedUri, handleMyDataFeedM, handleErrorM);
}

/**
 * Callback method to handle and display any error that occurs from
 * the API request.
 * @param {Object} e The error object returned by the Analytics API.
 */
function handleErrorM(e) {
  var msg = e.cause ? e.cause.statusText : e.message;
  msg = 'ERROR: ' + msg;
  alert(msg);
}

/**
 * Callback metod to handle all the data returned by GA Export API
 * @param {Object} myResultsFeedRoot the feed object
 *     retuned by the data feed.
 */
function handleMyDataFeedM(myResultsFeedRootM) {
  collectData(myResultsFeedRootM);
}

/**
 * Get data for each source in the passed in feed.
 * @param {Object} myResultsFeedRoot the Object returned by the GA Data Feed.
 */
function collectData(myResultsFeedRootM) {
  var entries = myResultsFeedRootM.feed.getEntries();
  if (entries.length == 0) {
    return;
  }

  // set a counter to the number of requests we'll be sending
  requestCount = entries.length;
  totalSources = entries.length;

  for (var idx = 0; idx < entries.length; idx++) {
     var source = entries[idx].getValueOf('ga:source');
     getMonthlySourceFeed(source);
  }
}

/**
 * Make a GA Data Feed request for monthly data for a particular source.
 * @param {String} source the source to get monthly data for.
 */
function getMonthlySourceFeed(source) {
  var profile = document.getElementById('aprofile').innerHTML;

  var chartyear = document.getElementById('yearChart').value;

  var myFeedUri = scope + '/data' +
    '?start-date=' + chartyear + '-01-01' +
    '&end-date=' + chartyear + '-12-31' +
    '&dimensions=ga:month' +
    '&metrics=ga:visits,ga:bounces,ga:pageviews,ga:timeOnSite' +
    '&sort=ga:month' +
    '&filters=ga:source%3D%3D' + source +
    '&ids=ga:' + profile;
	  

	
 
	
    myService3.getDataFeed(myFeedUri, handleSourceData, handleErrorM);
}

/**
 * Store the GA API request into an object and decrement the number of
 * @param {Object} sourceResults the Object passed back from the GA export API.
 */
function handleSourceData(sourceResults) {
  // get source parameter from Feed ID
  var source = sourceResults.feed.getId().getValue();
  var sourceArray = source.split('%3D%3D');
  sourceArray = sourceArray[1].split('&');
  source = sourceArray[0];

  mainChartData[source] = sourceResults.feed.getEntries();

  // since GA feed requests will return asynchronously, decrement the number
  // of requests that were sent.
  requestCount--;

  // if there are no more expected requests process the data into a dataTable
  if (requestCount == 0) {
    getDataTable();
  }
}

/**
 * Create a Google Visualization Data Table once all the data has been
 * collected from the GA Export API. Then visualize the Motion Chart.
 * Note: although we only add monthly data to our chart, the motion chart
 * will interpolate data between the months to display daily data.
 */
function getDataTable() {
  var dataTable = new google.visualization.DataTable();

  var chartyear = document.getElementById('yearChart').value;
  
  var rowCount = 0;
  var entries;
  var entry;

  dataTable.addColumn('string', 'Source');
  dataTable.addColumn('date', 'Date');
  dataTable.addColumn('number', 'Visits');
  dataTable.addColumn('number', 'Bounces');
  dataTable.addColumn('number', 'Pageviews');
  dataTable.addColumn('number', 'Time On Site');

  dataTable.addRows(totalSources * 12);

  for (source in mainChartData) {
    entries = mainChartData[source];
    for (var idx = 0; idx < entries.length; idx++) {
      entry = entries[idx];
      dataTable.setCell(rowCount, 0, source);
      dataTable.setCell(rowCount, 1, new Date(chartyear, idx, 1));
      dataTable.setCell(rowCount, 2, entry.getValueOf('ga:visits'));
      dataTable.setCell(rowCount, 3, entry.getValueOf('ga:bounces'));
      dataTable.setCell(rowCount, 4, entry.getValueOf('ga:pageviews'));
      dataTable.setCell(rowCount, 5, entry.getValueOf('ga:timeOnSite'));
      rowCount++;
    }
  }
  hideLoadingMessage();


  if (motionChart) {
    delete motionChart;
  }
  motionChart =
      new google.visualization.MotionChart(document.getElementById('motionChartDiv'));
  motionChart.draw(dataTable,
      {width: 800, height: 450, showSelectListComponent: 'true', showSidePanel: 'true'});
  delete mainChartData;
  mainChartData = {};
}

/**
 * Shows the loading message so people know to wait for the script.
 */
function showLoadingMessage() {
  var chartDiv = document.getElementById('motionChartDiv');
  var myDivString = '<div class="loading">' +
      '<p>Loading Data Please Wait...</p>' +
      '<img src="modules/analytics/images/ajax-loader.gif"/></div>';
  chartDiv.innerHTML = myDivString;
}

/**
 * Hides the loading message once the motion chart is ready to present.
 */
function hideLoadingMessage() {
  var chartDiv = document.getElementById('motionChartDiv');
  chartDiv.innerHTML = '';
}





