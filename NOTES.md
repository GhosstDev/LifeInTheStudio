# Table of Contents
1. [Table of Contents](#table-of-contents)
2. [TODO](#todo)
      1. [Frontend](#frontend)
3. [MySQL](#mysql)
   1. [Art Studio](#art-studio)
      1. [Accounts](#accounts)
      2. [Classes](#classes)
      3. [Assignments](#assignments)
      4. [Classwork](#classwork)
      5. [Trips](#trips)
      6. [Trip Roster Template](#trip-roster-template)
      7. [Tokens](#tokens)
      8. [Message Threads](#message-threads)
      9. [Message Thread Template](#message-thread-template)
      10. [Attachments](#attachments)
4. [Misc](#misc)
      1. [Fonts](#fonts)
      2. [Google Tag](#google-tag)
5. [Error Reference](#error-reference)


# TODO

### Frontend
    Base:
        - [x] Header tabs
        - [x] Logos
        - [x] Favicon
    Homepage:
        - [ ] Add gallery
        - [x] Get background images
        - [ ] Create intro page
        - [ ] Message Center
    Contact Us:
        - [ ] Create form
        - [ ] Create JavaScript functions
    Trips:
        - [x] Create brief trip template
        - [x] Create register forms
        ~~- [ ] Create detailed page template-~~
    Authorization Pages:
        - [x] Create login page
        - [x] Create signup page
        - [x] Create password reset page
        - [x] Create error handle function for all pages
    Accounts:
        - [ ] Account settings page
        - [ ] Notification widget in header
        - [ ] Notification JavaScript functions

# MySQL

## Art Studio

Tables:
- accounts
- classes
- assignments
- classwork
- trips
- trip-rosters
- tokens
- message-threads
- posts

### Accounts
| Field           | Data Type   | Attributes |
| :-------------- | ----------- | ---------: |
| uuid            | VARCHAR(16) |    PRIMARY |
| password        | TEXT        |            |
| email           | TEXT        |            |
| name            | TEXT        |            |
| current-classes | TEXT        |            |

***

### Classes
| Field       | Data Type   | Attributes |
| :---------- | ----------- | ---------: |
| class-id    | VARCHAR(16) |    PRIMARY |
| name        | TEXT        |            |
| description | TEXT        |            |
| roster      | TEXT        |            |
| mail-list   | TEXT        |            |

Roster JSON:
```json
{
    "class": [
        "{student id}"
    ]
}
```

***

### Assignments

| Field           | Data Type   | Attributes |
| :-------------- | ----------- | ---------: |
| assignment-id   | VARCHAR(16) |    PRIMARY |
| class-id        | VARCHAR(16) |            |
| name            | TEXT        |            |
| due-date        | DATETIME    |            |
| description     | TEXT        |            |
| server-location | TEXT        |            |

***

### Classwork

| Field         | Data Type   | Attributes |
| :------------ | ----------- | ---------: |
| classwork-id  | VARCHAR(16) |    PRIMARY |
| assignment-id | VARCHAR(16) |            |
| student-id    | VARCHAR(16) |            |
| submitted-at  | DATETIME    |            |
| file-location | TEXT        |            |
| file-name     | TEXT        |            |

***

### Trips                                            
| Field              | Data Type   |    Attributes |
| :----------------- | ----------- | ------------: |
| trip-id            | VARCHAR(16) |       PRIMARY |
| name               | TEXT        |               |
| short-desc         | TEXT        |               |
| signups-enabled    | BOOLEAN     | DEAFULT(TRUE) |
| signup-link        | TEXT        |               |
| signup-list        | TEXT        |               |
| mail-list          | TEXT        |               |
| signup-cutoff-date | DATE        |               |
| max-signups        | INT         |               |
| trip-start-date    | DATE        |               |
| trip-end-date      | DATE        |               |
| banner-image       | TEXT        |               |

***

### Trip Roster Template
| Field        | Data Type   | Attributes | Foreign Keys      |
|--------------|-------------|------------|-------------------|
| student-id   | VARCHAR(16) | PRIMARY    | `accounts`.`uuid` |
| signed-up-at | DATETIME    |            |                   |
| email        | TEXT        |            |                   |
| verified     | BOOLEAN     | DEFAULT(0) |                   |
| has-paid     | BOOLEAN     | DEFAULT(0) |                   |

***

### Tokens
| Field      | Data Type   | Attributes        |
| ---------- | ----------- | ----------------- |
| token-id   | INT         | PRIMARY  AI       |
| token      | VARCHAR(96) | UNIQUE            |
| token-key  | VARCHAR(64) | UNIQUE            |
| type       | TEXT        |                   |
| status     | BOOLEAN     | DEFAULT(TRUE)     |
| issued     | DATETIME    | CURRENT_TIMESTAMP |
| used       | DATETIME    | NULL              |
| token-data | TEXT        |                   |

***

### Message Threads
| Field         | Data Type   | Attributes |
| ------------- | ----------- | ---------- |
| thread-id     | VARCHAR(16) | PRIMARY    |
| participant1  | TEXT        |            |
| participant2  | TEXT        |            |
| message-count | INT         |            |

***

### Message Thread Template
*This is used as a template to create a message threat table*

| Field       | Data Type | Attributes                                               |
| ----------- | --------- | -------------------------------------------------------- |
| m-index     | INT       | PRIMARY   AI                                             |
| content     | TEXT      |                                                          |
| sent        | DATETIME  | CURRENT_TIMESTAMP                                        |
| from        | TEXT      | *userid of the sender*                                   |
| seen        | BOOLEAN   | DEFAULT(FALSE)                                           |
| attachments | TEXT      | *array that points to an index in the attachments table* |

### Attachments
| Field         | Data Type   | Attributes |
| ------------- | ----------- | ---------- |
| attachment-id | VARCHAR(16) | PRIMARY    |
| s3-pointer    | TEXT        |            |
| local-pointer | TEXT        |            |
| file-type     | TEXT        |            |
| file-name     | TEXT        |            |

# Misc


### Fonts
| Use      |        Font Name |
| :------- | ---------------: |
| Titles   | Permanent Marker |
| Quotes   |      Great Vibes |
| Body     |   Nanum Myeongjo |
| Captions |        Amatic SC |

### Google Tag

```
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-88725213-3"></script>
<script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', 'UA-88725213-3');</script>
```


# Error Reference
| Error                             | Reason                                                                            |
| --------------------------------- | --------------------------------------------------------------------------------- |
| err_no_action                     | GET parameter 'action' is missing                                                 |
| err_bad_action                    | GET parameter 'action' didn't point to a valid option                             |
| err_post_fields_missing           | Required POST parameter(s) were not present                                       |
| err_empty_post                    | No POST parameters were given                                                     |
| err_unknown_error                 | An unkown error occured, enable debug mode in the ini to see more information     |
| err_mysql_server_connection       | Couldn't connect to the MySQL server                                              |
| err_sql_query_failed              | SQL query error, enable debug mode to view more details                           |
| err_not_logged_in                 | User is not logged in but required to for the function                            |
| **auth.php errors**               |                                                                                   |
| err_invalid_password              | Password given by user and password for the account do not match                  |
| err_invalid_username              | Email given does not match with any accounts on record                            |
| err_email_taken                   | The email being submitted for registration already has an account linked to it    |
| err_invalid_token                 | The token and/or key given is invalid or has already been used                    |
| err_invalid_token_or_key          | The given token and/or token key is not valid or has already been used            |
| **aws.php errors**                |                                                                                   |
| err_aws_ses_error                 | An error occurred while attempting to use AWS SES                                 |
| **student.php errors**            |                                                                                   |
| err_duplicate_user                | The user already exists in the database, no need to invite them to register again |
| err_email_template_failed_to_read | Failed to retrieve email template file                                            |
| **student.php errors**            |                                                                                   |
| err_student_already_enrolled      | Student is already enrolled in the current class                                  |
