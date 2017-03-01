# whats done?	

	- The home screen displays a map with different event places highlighted on it. [done]

	- Selecting an event on the map displays the event details (name, location, event dates) 
		right below the map and the “Book your place” button become active. [done]

	- Clicking “Book your place” will take the user to the exposition hall map, it is a virtual map for the exposition 
		hall with different stands which he can navigate through it and book his stand. [done]

	- Booked stands is highlighted as booked, the logo of the booking company will be displayed on top of the stand, 
		below it the marketing documents (could be downloaded) and the contact details. [done]

	- Free stands is highlighted as free, and on top of it the price. [done]

	- The user can select any empty stand to book, clicking on an empty stand shows a popup with details of the stand, 
		a real image of it and a “Reserve” button. [done]

	- Clicking on reserve takes the user to the registration page where he supposed to provide: contact details, 
		upload marketing documents, company admin and company logo. [done]

	- Clicking on “Confirm Reservation” reserves the stand for the user, takes him to the exposition hall screen viewing 
		the booked stand with the user’s company details on it. [done]

	- Finally the company admin receives a report by mail about the users of the stand after the event is over. [done] 
	-- from where it can be checked? 
		well. a cron job need to be configured on deployment server which will call 
		script http://site.com/index.php/events/sendEventReport daily on 11:50pm. 
	-- how it works?
		it fetches all events with end date of today. if there is/are any event(s), it fetches all user details who visited stand along stand's admin email
		i.e 'finally they(company admin) will receive a report about the users who visited their stand on the event after it is over.'
	-- how to register user visit?
		on stand there is a button with label 'visit today', by pressing it, popup will be ther, enter sample user credentials
		i.e email: user1@mailinator.com pass: sampleuser1 or email:user2@mailinator.com pass:sampleuser2
	-- any test case
		yes, uncoment from line 444 to 447 in application/models/events_model.php & comment 449-451

Instructions to install and configure
-------------------------------------	
	# steps to configure	
		- copy folder 'code' content & paste it to your server root directory e.g public_html
		- update your site url in code/application/config/config.php line 26
			e.g if your site url is 'vtexpositions.com' in
			code/application/config/config.php 
			update $config['base_url'] = 'vtexpositions.com'
			
	# Instructions to create and initialize the databases
		- from zip folder 'Database' copy script 'virtualexposition.sql' & execute in MySQL
			it will create required databases 'virtualexposition' along tables & sample data.
		- update code/application/config/database.php according to your server details.
			i.e set username,password & database name (if you have chosed different)
	
	
