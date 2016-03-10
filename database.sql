CREATE TABLE textingjournal.`questionfrequency` ( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`type`  varchar(240), 
PRIMARY KEY (`id`)
)

CREATE TABLE textingjournal.`users` ( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`fullname`  varchar(240), 
`emailaddress` varchar(240),
`username` varchar(240), 
`password` varchar(240),
`phonenumber` varchar(240),
`timezone` varchar(240),

`profilepic` varchar(240),
`backgroundpic` varchar(240),

`questionfrequencyid` int(11) NOT NULL,
`isDeleted` int(2),
PRIMARY KEY (`id`),
FOREIGN KEY (questionfrequencyid) REFERENCES questionfrequency(id)
)



CREATE TABLE textingjournal.`question` ( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`question`  varchar(240), 
PRIMARY KEY (`id`)
)

CREATE TABLE textingjournal.`entries` ( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`date` DATE NOT NULL,
`text`  varchar(240),
`phonenumber`  varchar(240),
`questionid` int(11) NOT NULL,
`userid` int(11) NOT NULL,
`about` varchar(240),
`messageSid` varchar(240),
`smsid` varchar(240),
`accountsid` varchar(240),
`messagingservicesid` varchar(240),
`nummedia` int(11),
`isDeleted` int(2),
PRIMARY KEY (`id`),
FOREIGN KEY (questionid) REFERENCES questiondate(id),
FOREIGN KEY (userid) REFERENCES users(id)
)

CREATE TABLE textingjournal.`questiondate` ( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`questionid`  varchar(240),
`date` DATE NOT NULL,
PRIMARY KEY (`id`),
FOREIGN KEY (questionid) REFERENCES question(id),
)