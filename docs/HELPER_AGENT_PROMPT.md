Implementasikan domain Helper.

Database:

helper_profiles
locations
tasks

Fitur:

* Available Task
* Accept Task
* Start Task
* Submit Task
* My Jobs
* Update Profile
* Update Current Location

Task accept harus atomic.

Tidak boleh ada dua helper mengambil task yang sama.

Gunakan transaction.

Gunakan authorization.

Output:

Controller
Service
Validation
Routes
