# Sortir-ENI
PHP/Symfony Project for ENI Wed Developer Course.<br>

## Development </br>
Symfony 5.2 / PHP 8.0

## Website functionality - User management:</br>
-Users can log-in (using user name or email), save their log-in info, modify account information, and view profiles of other users.</br>
-Users can upload photos for their profile (the link to the photos is stored in the database).</br>
-Users can request to reset their email, in which case a password reset token is created and a link is sent to their registered email address that is only valid for 24 hours.</br>

## Website functionality - Event management:</br>
-Users can view details of all events up to 30 days before.</br>
-Events can be dynamically filtered based on campus/location name/dates/subscriptions and other criteria.<br>
-Event status is updated based on multiple factors: created/open(allows subscriptions)/closed(subscription date passed)/active(event ongoing)finished(event date passed)/cancelled(user cancelled their event).<br>
-Users can create new events.</br>
-Users can subscribe to or unsubscribe from events. They can't subscribe to their own events. Event spots are limited and are automatically updated based on subscriptions.</br>
-Users can edit their events before the event start date.</br>
-Users can delete their events when no active subscriptions are present.<br>
-Users can cancel events in which case all active subscriptions are also cancelled.<br>

## Website admin:</br>
-Admin can register users individually or in groups with CSV files.</br>
-Admin can deactivate/reactivate users or delete users (dynamic).<br>
-Admin can add new city/campus locations (dynamic).<br>
-Admin can delete other users events (dynamic).<br>

## Notes:</br>
...
