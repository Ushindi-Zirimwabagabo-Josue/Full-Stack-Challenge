# Example Full-Stack Challenge

We're excited that you're interested in performing the coding challenge.
The purpose of this test is to see how you approach problems as well as to evaluate the quality of your code.

## Challenge description

This challenge imagines that we have a social media platform that is under attack from spam. We have implemented a reporting system for users that lets them report spam to the platform, and our spam protection team.  

The challenge is to create a small full stack application for our spam protection team that consists of a server and a web based UI in order to manage reported content.

The UI should look something like:

![Reporting listing](images/wireframe.png)

We provide an example listing response ([`data/reports.json`](data/reports.json)) that you can use as the basis of your listing. Please fill the appropriate fields in the wireframe, ignore the "Details" link.

Furthermore we need a way to _block_ the content and _resolve_ those reports. The two buttons in the UI should do a call to your backend service in order to block the content or to resolve the ticket. You are free to implement the blocking as you want, however the resolving should be defined as a `PUT` request to an endpoint with this structure `/reports/:reportId`. An example request for how to update a report is in [`data/update_ticket_request.json`](data/update_ticket_request.json).

- **`Block`:** Means that the content should no longer be available to users
- **`Resolve`:** Means that the report is considered "resolved", and it is no longer visible to the spam protection team
- **`Details`:** Functionality can be ignored.

### Notes

- Please don't spend more than 3 hours on this
- Use PHP for the the backend part
- Use React or another modern frontend framework
- Utilize some kind of caching method, preferribly APCu
- Only use the suggested technologies if you are familiar with them and can complete the challenge within the time limit
- Provide the solution source code as a link to the code repository - please provide invites to a private repo if you don't want it public

### What we're looking for:

- High code quality (you should favor quality over quantity)
- Pragmatic technical choices
- A runnable full-stack application

