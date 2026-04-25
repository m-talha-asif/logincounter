# Login Counter (block_logincounter)

**Login Counter** is a comprehensive engagement-tracking block plugin for Moodle. It provides users with quick insights into their account activity while simultaneously serving as a powerful, AJAX-driven time-tracking reporting tool for course administrators and managers. 

## ✨ Features

* **Personalized Login Statistics**: Every user who views the block sees their own personal details, including their User ID, Username, Total Login Count, and their Last Login timestamp.
* **Student Time Tracking**: Regular users are presented with a "My Course Time" table that automatically lists all the courses they are enrolled in alongside the total active time they've spent in each.
* **Admin / Manager Reporting**: Users with the `block/logincounter:viewalltimes` capability (like Managers and Editing Teachers) see an "Admin Time Tracker" instead. They can select any course from a dropdown menu to instantly view a report of all enrolled students and their respective time spent.
* **Smart Time Calculation**: The plugin calculates active time by analyzing event gaps in Moodle's `logstore_standard_log`. It correctly formats time into hours, minutes, and seconds, and automatically times out sessions if a user is inactive for more than 15 minutes.
* **Seamless AJAX UI**: Data is fetched dynamically in the background, ensuring fast page load times and allowing admins to switch between course reports without reloading the page.

## 📋 Requirements

* **Moodle Version:** 4.0 or higher (Requires `2022041900`).
* Moodle's Standard Log store (`logstore_standard_log`) must be enabled to calculate time spent accurately.

## 🚀 Installation

1. Download the plugin and extract the files.
2. Rename the extracted folder to `logincounter` (if it isn't already).
3. Place the `logincounter` folder into the `blocks/` directory of your Moodle installation.
    * The path should be: `[moodle_root]/blocks/logincounter`
4. Log in to your Moodle site as an Administrator.
5. Go to **Site administration > Notifications** to complete the plugin installation.

## ⚙️ Usage

1. Turn editing on within your Moodle course or on your Dashboard.
2. Click **Add a block** and select **Login Counter**.
3. The block will automatically detect the user's role:
    * **Students** will see their login count and the "My Course Time" table loading their active times.
    * **Admins/Teachers** will see the "Admin Time Tracker" block, allowing them to pull reports for any active course.

## 📄 License
This plugin is developed for Moodle and inherits the GNU General Public License (GPL) standards utilized by the Moodle core platform.
