Run locally:
   - `./runtimes/8.0/bin/install.sh`
   - `./vendor/bin/sail artisan migrate` (try more time if connection will be refused while mysql starting)
   - use regular `./vendor/bin/sail` commands after first install

How it works:
  - Every minute sends emails `app/Console/Commands/SendTrackEmail.php`
  - Listens for View and Url Clicks(TODO)
  - Every minute checks if there are undelivered emails `app/Console/Commands/CheckDeliveryFailure.php`
   - yes: fire `app/Jobs/ProcessDeliveryFailure.php` and delete user, fire `app/Jobs/DeleteDeliveryFailureRecordJob` and delete message in inbox
   - no: nothing to do
  - View event is fired:
     - Sets user is alive field to true
  - Link clicked event is fired(TODO):
     - Unsubscribe: Remove user from mailing list
     - Register: Set email verified and ask to enter name and password. Send welcome guide email.


