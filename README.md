# SendGrid Inbound Email Parser

This is a small PHP application, built on top of [the Mezzio framework][mezzio-docs], that shows how to work with [SendGrid's Inbound Email Parse Webhook][email-parse-webhook-docs].

If you're not familiar with the Webhook, here's a quick excerpt from the documentation:

> SendGrid can parse the attachments and contents of incoming emails.
> The Parse API will (then) POST the parsed email to a URL that you specify. 

In short, when emails are sent to a pre-configured domain, they are received and parsed by the Inbound Email Parse Webhook. 
This then sends a POST request to an endpoint that you specified in the webhook configuration (which is to your application/API).

## Prerequisites

To use the application, you need the following:

- PHP 8.1 or 8.2 with the following extensions:
  - [ctype][ext-ctype]
  - [dom][ext-dom]
  - [iconv][ext-iconv]
  - [json][ext-json]
  - [libxml][ext-libxml]
  - [mbstring][ext-mbstring]
  - [pcre][ext-pcre]
  - [phar][ext-phar]
  - [tokenizer][ext-tokenizer]
  - [xml][ext-xml]
  - [xmlwriter][ext-xmlwriter]
- [A Twilio Account](https://www.twilio.com/try-twilio) 
- [A SendGrid account](https://signup.sendgrid.com/)
- A domain that you can use, along with access to the DNS records
- [ngrok][ngrok-url] and a free ngrok account

## Why was the application written?

The application was inspired after recent dealings with an Australian government department.

In short, I had sent the department an application, along with the supporting documentation. 
After not hearing anything for nearly four weeks, I called their support centre. 

As it turned out, the application required further supporting documentation, which I had to bring the documentation in to a local office or post it. 
After I did that, it would be added to the application, and the application would continue being processed. 

I would have _much_ preferred to be able to email the information and have it be included with the application automatically, saving a good amount of time and effort.

So, in my frustration, I looked around to see if there was any technology which did what I was after.
It was then that I learned about SendGrid's Inbound Email Parser.

It wasn't all that I needed, but allowed me to build a proof of concept, which is what this application is.

## Application overview

The application creates a note on a user's account (stored in a database), based on the POST data that it receives from SendGrid.
To determine the account to update, the email subject has to match one of the following two (case-insensitive) patterns:

- `Reference ID: <Reference ID>`
- `Ref ID: <Reference ID>`

`<Reference ID>` is a 14 character string that can contain both lower and uppercase letters and any digit between 0 and 9 (inclusive).

If it does, then the message's body and any attachments are extracted and added as a note on the user's account. 
Following that, the user is sent an MMS confirmation that the email has been received and the note created on their account.
The MMS lists the names of any attachments found on the email, and includes the message's body as a text file attachment.
The attachment is retrieved by calling the application's second route, supplying the new note's id.

If the email subject doesn't match one of the required patterns, then a JSON response is returned stating this.

The application has two routes: 

- The first (the default) receives POST requests from SendGrid's Inbound Email Parser and creates a note from the email.
- The second receives GET requests containing a note's ID in the route's path, and returns a text file containing a copy of the email body (message) stored in the note.

## Usage

To use the application, first, copy _.env.local_ as _.env_.
Then, from the Twilio Console, set your Twilio Account SID, Auth Token, and phone number as the values for the three variables in _.env_.
After that, [follow the instructions for setting up the Inbound Parse Webhook][inbound-parse-webhook-setup-docs].

When that's done, clone the code locally, install PHP's dependencies, and run the database migrations, by running the following commands.

```bash
git clone git@github.com:settermjd/inbound-parser.git inbound-parser
cd inbound-parser
composer install \
    --no-dev --no-ansi --no-plugins --no-progress --no-scripts \
    --classmap-authoritative --no-interaction \
    --quiet
composer mezzio doctrine:migrations:migrate
```

You'll also have to (manually) add at least one record to the `user` table in the database.

Then, run the following command to start ngrok and have it make port 8080 publicly available on the internet.

```bash
ngrok http 8080
```

Then, in _config/autoload/app.global.php_, change the value for `baseUrl` to the ngrok Forwarding URL.

Finally, run the following command to run the application:

```bash
ENVIRONMENT=development composer serve
```

Now, send an email to the email address that you configured with the Inbound Parse Webhook. 
Shortly afterward, you should see a new record in  

[email-parse-webhook-docs]: https://docs.sendgrid.com/for-developers/parsing-email/inbound-email 
[inbound-parse-webhook-setup-docs]: https://docs.sendgrid.com/for-developers/parsing-email/setting-up-the-inbound-parse-webhook
[mezzio-docs]: https://docs.mezzio.dev/
[ext-ctype]: https://www.php.net/manual/en/intro.ctype.php
[ext-dom]: https://www.php.net/manual/en/intro.dom.php
[ext-iconv]: https://www.php.net/manual/en/intro.iconv.php
[ext-json]: https://www.php.net/manual/en/intro.json.php
[ext-libxml]: https://www.php.net/manual/en/intro.libxml.php
[ext-mbstring]: https://www.php.net/manual/en/intro.mbstring.php
[ext-pcre]: https://www.php.net/manual/en/intro.pcre.php
[ext-phar]: https://www.php.net/manual/en/intro.phar.php
[ext-tokenizer]: https://www.php.net/manual/en/intro.tokenizer.php
[ext-xml]: https://www.php.net/manual/en/intro.xml.php
[ext-xmlwriter]: https://www.php.net/manual/en/intro.xmlwriter.php
[ngrok-url]: https://ngrok.com/
