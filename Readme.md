# Availability Calendar

Availability Calendar is a Bootstrap/PHP calendar to show availability and synchronise with other iCalendar (e.g. AirBNB, HomeAway, ...)

You can manage the availability via an admin panel.

![image](https://pir-d.com/assets/img/Calendar.png)

**Beta version**

## Dependencies
- Bootstrap  
`<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">`

- Jquery  
`<script src="https://code.jquery.com/jquery-3.5.1.min.js"
         integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
         crossorigin="anonymous"></script>`
         
- Font Awesome  
`<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">`

## Installation
`git clone https://github.com/PiR1/Availability-Calendar.git`

Change credentials for SQL connection `php/config/dbclass.php`
```PHP
 private $host = "localhost"; //SQL server url
 private $username = "root"; //username
 private $password = ""; //password
 private $database = "calendar"; //database name
 private $port = 3308; //SQL serve port
```

Go to the index.html and it will configure the database.
It will create a default admin user.

Default username: `admin`  
Default password: `admin` 

To change the availability of a day, go to the admin panel, log in with your credentials and just click on a date to change its state.
On the right side of the panel you can change the links of the calendars you want to synchronize with.

The link to your calendar is: /php/ical/Calendar.ics 


## Get Started
To include your calendar on your page, you need to include Bootstrap, Jquery and Font awesome (see dependencies).

You also need to include:  
```html
<link href="assets/css/style.css" rel="stylesheet" type="text/css" />
```
```html
<script src="assets/js/dateParse.js"></script>
<script src="assets/js/calendar.js"></script>
```

If the Calendar folder is not the same as your page, define the variable `url_ajax_event`  
For example if you're in the root and your Calendar folder is Calendar, add this:
```html
<script>
    var url_ajax_event = "Calendar/";
</script>
```

Put the class `calendar` where you want to have your Calendar
```html
<div class="calendar"></div>
```

