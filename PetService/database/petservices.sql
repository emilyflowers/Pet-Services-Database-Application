--
-- Table structure for employee
--

CREATE TABLE employee (
    EId INTEGER AUTO_INCREMENT,
    Name VARCHAR(50) NOT NULL,
    IsAdmin BOOLEAN NOT NULL DEFAULT FALSE,
    Password VARCHAR(255) NOT NULL,
    PRIMARY KEY (EId)
)

-- 
-- Dumping data for table employee
-- 

INSERT INTO employee (EId, Name, IsAdmin, Password)
VALUES
(1, 'Chaya Cook', FALSE, 'q6TwsVNE'),
(2, 'Ezekiel Dudley', FALSE, 'HrqNPAA4'),
(3, 'Hadleigh Meyers', FALSE, 'xZQ0enT9'),
(4, 'Julien Adams', TRUE, 'GhYhHby7'),
(5, 'Stella Henry', TRUE, 'WE0Xkgt2'),
(6, 'Carlos Quintana', FALSE, 'NYU9CAP1'),
(7, 'Kenia Heath', FALSE, 'QE29a9t0'),
(8, 'Lionel Marks', FALSE, 'RYcy1MyH'),
(9, 'Monica Jacobs', FALSE, '4Yc7yFjz'),
(10, 'Bryan Potter', TRUE, 'BNDL9AtE'),
(11, 'Rory Palacios', TRUE, 'tRuAW6ry'),
(12, 'Thaddeus Hess', FALSE, 'pmHz8khP'),
(13, 'Kaliyah George', FALSE, 'n3EaahEn'),
(14, 'Mark Shelton', TRUE, 'NfAkQ7Da'),
(15, 'Makenzie Crane', FALSE, 'RMqsQ2Ct');

-- --------------------------------------------------------

--
-- Table structure for owner
--

CREATE TABLE owner (
    OId INTEGER AUTO_INCREMENT,
    Name VARCHAR(50)NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    PhoneNumber VARCHAR(15) NOT NULL,
    Address VARCHAR (255) NOT NULL,
    PRIMARY KEY (OId)
)

--
-- Dumping data for table owner
--

INSERT INTO owner (Name, PhoneNumber, Email, Address, Password) 
VALUES
(1, 'Cassidy Clay', '(242) 596-4531', 'cassidyclay2417@outlook.com', '331 Integer Rd.', 'Uyn0T3hh'),
(2, 'Paloma Curtis', '(771) 988-0565', 'palomacurtis@protonmail.org', 'Ap #865-5862 Integer Rd.', 'sYsnRn91'),
(3, 'Velma Holcomb', '(356) 587-3516', 'velmaholcomb8055@google.edu', 'Ap #382-7193 Pellentesque Rd.', 'crZkvTc0'),
(4, 'Lenore Cameron', '(668) 514-1273', 'lenorecameron@icloud.couk', '592-3449 Congue Rd.', 'fT8m4Rgg'),
(5, 'Adrienne Hardin', '(719) 620-1772', 'adriennehardin@outlook.net', 'Ap #156-4512 Laoreet Street', 'rmFfeB5v'),
(6, 'April Hutchinson', '(854) 301-0568', 'aprilhutchinson5279@google.couk', '297-2517 Lacus. Avenue', 'QfnmyJ40'),
(7, 'Carter Nichols', '(842) 612-7896', 'carternichols@outlook.net', 'Ap #986-3874 Lorem, Road', '3ZP9EPwE'),
(8, 'Carter Mccray', '(937) 628-4422', 'cartermccray@aol.net', 'Ap #821-5118 Nunc Rd.', '36HLDpF4'),
(9, 'Jamalia Chase', '(939) 374-6588', 'jamaliachase@aol.net', '584-4599 Malesuada Rd.', 'y42v347m'),
(10, 'Adena Pratt', '(273) 386-3224', 'adenapratt7626@protonmail.org', '821-6651 Lorem Ave', '5JAA0rF3'),
(11, 'Rebecca Taylor', '(581) 746-3758', 'rebeccataylor@protonmail.edu', 'Ap #535-7218 In Rd.', 'fsX9nzA3'),
(12, 'Abel Dudley', '(451) 433-5563', 'abeldudley@protonmail.com', 'Ap #417-8587 Cras Street', '1gxpfLqT'),
(13, 'Kenneth Ford', '(974) 452-6226', 'kennethford@google.edu', '574-3543 Dolor Street', 'UTPyKg6y'),
(14, 'Gray Olson', '(480) 286-7647', 'grayolson@outlook.com', 'P.O. Box 730, 6790 Pellentesque Av.', 'dhyViY0V'),
(15, 'Michael Dean', '(364) 813-5773', 'michaeldean@protonmail.ca', '6267 Commodo Rd.', 'uWvF8t6p'),
(16, 'Travis Ramos', '(231) 433-2542', 'travisramos@yahoo.net', 'Ap #876-4207 Cras Av.', '9cUf8Unb'),
(17, 'Tanek Gomez', '(138) 373-6531', 'tanekgomez@outlook.ca', '440-5614 Pulvinar St.', '0tmhErVJ'),
(18, 'Graiden Gross', '(785) 292-5033', 'graidengross6084@hotmail.com', 'Ap #467-5818 Sagittis. Ave', 'h2d6obBF'),
(19, 'Cora Chang', '(298) 859-6737', 'corachang1296@yahoo.net', '1371 Consectetuer Ave', 'js19Y1TF'),
(20, 'Priscilla Boyle', '(951) 864-5872', 'priscillaboyle2582@protonmail.com', '7938 Malesuada St.', 'qgHc4qEk'),
(21, 'Roary Dunn', '(452) 758-2180', 'roarydunn@protonmail.ca', '8226 Penatibus Rd.', 'Df741b2R'),
(22, 'Winter Meadows', '(743) 246-7574', 'wintermeadows@protonmail.couk', 'Ap #324-6484 Arcu. Avenue', '3Bn6tcmQ'),
(23, 'Mechelle Pittman', '(416) 588-7344', 'mechellepittman@google.ca', '6098 Ornare Avenue', '2LrNa3rY'),
(24, 'Uma King', '(965) 281-9724', 'umaking@google.net', 'P.O. Box 698, 9616 Tempor Rd.', 'WeMmjo5e'),
(25, 'Finn Jimenez', '(726) 839-7611', 'finnjimenez1146@outlook.edu', '571-1284 Amet St.', 'Z9q3VrHc');

-- --------------------------------------------------------

--
-- Table structure for table pet
--

CREATE TABLE pet (
    PId INTEGER AUTO_INCREMENT,
    OId INTEGER NOT NULL,
    IsVaccinated BOOLEAN NOT NULL,
    Name VARCHAR(50) NOT NULL,
    Birthdate DATE NOT NULL,
    Species VARCHAR(20),
    IsFixed BOOLEAN NOT NULL,
    LastVetApp DATE,
    LastVisit DATE,
    PRIMARY KEY (PId),
    FOREIGN KEY (OId) REFERENCES Owner (OId) ON DELETE CASCADE
)

--
-- Dumping data for table pet
--

INSERT INTO pet (PId, OId, Name, Birthdate, Species, IsFixed, IsVaccinated, LastVetApp, LastVisit) 
VALUES
(1, 13, 'Spencer', '2022-07-16', 'cat', FALSE, TRUE, '2026-07-10', '2025-08-05'),
(2, 10, 'Lovey', '2023-10-14', 'dog', FALSE, FALSE, NULL, '2025-04-03'),
(3, 23, 'Misty', '2021-03-11', 'dog', TRUE, FALSE, '2025-03-27', '2026-08-23'),
(4, 5, 'Star', '2022-01-05', 'dog', TRUE, FALSE, '2022-08-01', '2025-07-15'),
(5, 21, 'Opie', '2021-02-14', 'cat', FALSE, FALSE, '2024-12-03', '2026-01-02'),
(6, 19, 'India', '2020-12-31', 'cat', FALSE, TRUE, '2021-11-08', '2025-04-07'),
(7, 2, 'Lucky', '2022-09-10', 'dog', TRUE, TRUE, '2023-10-06', '2025-06-20'),
(8, 3, 'Lou', '2021-08-11', 'dog', TRUE, FALSE, '2024-11-05', '2026-09-11'),
(9, 18, 'Jade', '2023-09-20', 'cat', TRUE, TRUE, '2023-10-25', '2026-03-27'),
(10, 12, 'Godiva', '2019-10-29', 'cat', FALSE, FALSE, NULL, '2025-05-28'),
(11, 20, 'Chip', '2022-05-14', 'dog', FALSE, FALSE, NULL, '2026-01-28'),
(12, 16, 'Lexi', '2024-04-01', 'cat', TRUE, TRUE, '2024-08-20', '2026-08-25'),
(13, 7, 'Macho', '2024-01-25', 'cat', TRUE, TRUE, '2024-09-13', '2025-05-07'),
(14, 17, 'Timmy', '2020-10-15', 'dog', TRUE, TRUE, '2025-05-05', '2026-08-28'),
(15, 3, 'Scoobie', '2022-12-27', 'dog', TRUE, TRUE, '2026-05-31', '2026-04-02'),
(16, 14, 'Chessie', '2024-11-29', 'cat', TRUE, FALSE, '2026-10-16', '2025-03-23'),
(17, 5, 'Doc', '2021-07-26', 'cat', TRUE, FALSE, '2023-06-12', '2026-07-29'),
(18, 7, 'Beau', '2022-12-20', 'dog', FALSE, TRUE, '2026-01-08', '2025-05-12'),
(19, 21, 'Reggie', '2018-09-14', 'dog', TRUE, TRUE, '2023-08-21', '2026-06-04'),
(20, 14, 'Frosty', '2020-07-13', 'dog', FALSE, TRUE, '2022-03-23', '2025-12-06'),
(21, 6, 'Sebastian', '2018-06-21', 'dog', TRUE, TRUE, '2024-10-25', '2026-10-12'),
(22, 9, 'Miasy', '2023-10-01', 'cat', FALSE, FALSE, NULL, '2026-08-27'),
(23, 22, 'Rock', '2023-02-07', 'dog', TRUE, FALSE, '2025-11-07', '2026-03-04'),
(24, 8, 'Pudge', '2024-03-13', 'cat', FALSE, FALSE, NULL, '2026-02-16'),
(25, 11, 'Sterling', '2020-10-15', 'cat', TRUE, FALSE, '2026-07-08', '2026-02-17'),
(26, 25, 'Logan', '2024-06-24', 'dog', TRUE, TRUE, '2025-03-31', '2026-10-08'),
(27, 11, 'Joey', '2022-03-25', 'cat', TRUE, TRUE, '2022-05-19', '2026-01-20'),
(28, 18, 'Tiger', '2022-08-01', 'cat', FALSE, TRUE, '2022-12-21', '2025-11-01'),
(29, 22, 'Chubbs', '2023-09-18', 'cat', TRUE, TRUE, '2026-05-12', '2025-11-12'),
(30, 16, 'Freddie', '2021-10-06', 'dog', FALSE, TRUE, '2023-04-20', '2026-02-26'),
(31, 17, 'Fred', '2022-01-17', 'cat', TRUE, TRUE, '2022-05-13', '2025-06-23'),
(32, 14, 'Luna', '2019-11-18', 'cat', TRUE, TRUE, '2025-06-17', '2025-11-28'),
(33, 2, 'Morgan', '2023-02-12', 'dog', TRUE, FALSE, '2025-02-19', '2026-05-03'),
(34, 10, 'Titan', '2019-09-19', 'dog', TRUE, FALSE, '2023-03-30', '2025-04-03'),
(35, 11, 'Hobbes', '2023-01-25', 'cat', TRUE, FALSE, '2026-04-30', '2025-12-18'),
(36, 1, 'King', '2024-02-11', 'dog', FALSE, TRUE, '2025-08-26', '2025-06-20'),
(37, 7, 'Spotty', '2023-08-01', 'dog', FALSE, TRUE, '2026-02-22', '2025-06-13'),
(38, 15, 'Jellybean', '2025-02-07', 'cat', TRUE, FALSE, '2026-06-23', '2026-08-31'),
(39, 16, 'Brutus', '2018-06-14', 'dog', FALSE, FALSE, NULL, '2025-05-17'),
(40, 13, 'Libby', '2019-10-20', 'dog', TRUE, FALSE, '2024-04-11', '2026-09-24'),
(41, 20, 'Sweetie', '2023-01-24', 'cat', TRUE, FALSE, '2024-10-13', '2025-08-02'),
(42, 25, 'Molly', '2022-03-23', 'cat', FALSE, TRUE, '2024-06-11', '2026-04-20'),
(43, 11, 'Jesse', '2023-04-13', 'dog', TRUE, TRUE, '2023-10-25', '2026-03-14'),
(44, 4, 'Kira', '2020-06-02', 'dog', TRUE, TRUE, '2025-08-07', '2026-08-10'),
(45, 21, 'Arrow', '2021-07-06', 'dog', FALSE, FALSE, NULL, '2026-02-08');

-- --------------------------------------------------------

-- 
-- Table structure for table appointment
--

CREATE TABLE appointment (
    AId INTEGER AUTO_INCREMENT,
    PId INTEGER NOT NULL,
    EId INTEGER NOT NULL,
    Grooming BOOLEAN NOT NULL DEFAULT FALSE,
    Boarding BOOLEAN NOT NULL DEFAULT FALSE,
    Training BOOLEAN NOT NULL DEFAULT FALSE,
    StartDate DATETIME NOT NULL,
    EndDate DATETIME NOT NULL,
    PRIMARY KEY (AId),
    FOREIGN KEY (PId) REFERENCES Pet (PId),
    FOREIGN KEY (EId) REFERENCES Employee (EId)
)

-- 
-- Dumping data for table appointment
--

INSERT INTO appointment (AId, PId, EId, StartDate, EndDate, Boarding, Grooming, Training)
VALUES
(1, 12, 8, '2026-08-25', '2026-08-25', FALSE, FALSE, TRUE),
(2, 27, 12, '2026-01-18', '2026-01-20', TRUE, TRUE, FALSE),
(3, 15, 9, '2026-04-02', '2026-04-02', FALSE, TRUE, FALSE),
(4, 7, 9, '2025-06-17', '2025-06-20', TRUE, FALSE, FALSE),
(5, 39, 7, '2025-05-17', '2025-05-17', FALSE, FALSE, FALSE),
(6, 18, 9, '2025-05-12', '2025-05-12', FALSE, TRUE, TRUE),
(7, 35, 13, '2025-12-16', '2025-12-18', TRUE, TRUE, FALSE),
(8, 36, 7, '2025-06-15', '2025-06-20', TRUE, TRUE, TRUE),
(9, 29, 13, '2025-11-07', '2025-11-12', TRUE, TRUE, TRUE),
(10, 33, 12, '2026-05-03', '2026-05-03', FALSE, TRUE, TRUE),
(11, 31, 2, '2025-06-22', '2025-06-23', TRUE, FALSE, FALSE),
(12, 9, 15, '2026-03-24', '2026-03-27', TRUE, FALSE, FALSE),
(13, 4, 1, '2025-07-14', '2025-07-15', TRUE, TRUE, FALSE),
(14, 3, 2, '2026-08-23', '2026-08-23', FALSE, TRUE, TRUE),
(15, 34, 15, '2025-04-01', '2025-04-03', TRUE, TRUE, TRUE),
(16, 30, 7, '2025-10-08', '2025-10-11', TRUE, TRUE, TRUE),
(17, 19, 15, '2026-06-04', '2026-06-04', FALSE, FALSE, FALSE),
(18, 32, 2, '2025-11-27', '2025-11-28', TRUE, FALSE, FALSE),
(19, 42, 1, '2026-04-17', '2026-04-20', TRUE, FALSE, TRUE),
(20, 26, 1, '2026-10-06', '2026-10-08', TRUE, FALSE, FALSE),
(21, 28, 13, '2025-10-29', '2025-11-01', TRUE, FALSE, FALSE),
(22, 41, 8, '2025-07-30', '2025-08-02', TRUE, FALSE, FALSE),
(23, 8, 3, '2026-09-09', '2026-09-11', TRUE, TRUE, FALSE),
(24, 22, 15, '2026-08-26', '2026-08-27', TRUE, TRUE, TRUE),
(25, 20, 6, '2025-12-05', '2025-12-06', TRUE, FALSE, FALSE),
(26, 21, 3, '2026-10-11', '2026-10-12', TRUE, TRUE, FALSE),
(27, 11, 8, '2026-01-26', '2026-01-28', TRUE, TRUE, FALSE),
(28, 14, 1, '2026-08-26', '2026-08-28', TRUE, TRUE, FALSE),
(29, 43, 7, '2026-03-13', '2026-03-14', TRUE, FALSE, FALSE),
(30, 6, 13, '2025-04-03', '2025-04-07', TRUE, TRUE, TRUE),
(31, 23, 6, '2026-03-04', '2026-03-04', FALSE, TRUE, FALSE),
(32, 5, 3, '2025-12-31', '2026-01-02', TRUE, TRUE, FALSE),
(33, 24, 15, '2026-02-12', '2026-02-16', TRUE, FALSE, FALSE),
(34, 1, 9, '2025-08-04', '2025-08-05', TRUE, TRUE, TRUE),
(35, 10, 1, '2025-05-26', '2025-05-28', TRUE, FALSE, TRUE),
(36, 25, 3, '2026-02-16', '2026-02-17', TRUE, TRUE, TRUE),
(37, 17, 6, '2026-07-29', '2026-07-29', FALSE, FALSE, TRUE),
(38, 38, 2, '2026-08-29', '2026-08-31', TRUE, FALSE, FALSE),
(39, 37, 8, '2025-06-11', '2025-06-13', TRUE, TRUE, FALSE),
(40, 13, 6, '2025-05-04', '2025-05-07', TRUE, TRUE, FALSE),
(41, 16, 12, '2025-03-21', '2025-03-23', TRUE, TRUE, FALSE),
(42, 44, 12, '2026-08-09', '2026-08-10', TRUE, FALSE, FALSE),
(43, 45, 12, '2026-02-08', '2026-02-08', FALSE, TRUE, FALSE),
(44, 40, 13, '2026-09-23', '2026-09-24', TRUE, TRUE, FALSE),
(45, 2, 3, '2025-04-01', '2025-04-03', TRUE, TRUE, FALSE),
(46, 30, 8, '2026-02-24', '2026-02-26', TRUE, FALSE, FALSE),
(47, 19, 7, '2025-04-07', '2025-04-10', TRUE, TRUE, FALSE),
(48, 32, 6, '2025-09-22', '2025-09-21', TRUE, FALSE, FALSE),
(49, 42, 2, '2025-09-04', '2025-09-05', TRUE, FALSE, FALSE),
(50, 26, 9, '2025-07-07', '2025-07-08', TRUE, FALSE, TRUE);

-- --------------------------------------------------------

--
-- Table structure for table review
--

CREATE TABLE review (
    AId INTEGER NOT NULL,
    Body TEXT DEFAULT NULL,
    Date DATE NOT NULL DEFAULT (CURRENT_DATE),
    Rating INTEGER NOT NULL,
    PRIMARY KEY (AId),
    FOREIGN KEY (AId) REFERENCES Appointment (AId)
)

--
-- Dumping data for table review
--

INSERT INTO review (AId, Date, Rating, Body)
VALUES
(28, '2026-08-29', 5, 'They took great care of my boy Timmy! After his bath and nail trimming, he came back looking like a whole different pup!'),
(15, '2025-04-04', 5, 'Makenzie was such a good caretaker for my baby! Great service!'),
(34, '2025-08-06', 3, 'A bit disappointed with the quality of service. Seemed like minimal effort or care was put into training and nail trimming, but kept my boy safe and happy, so overall satisfied.'),
(42, '2026-08-11', 4, 'Incredibly helpful service for watching over a pet for an extended of time.'),
(36, '2026-02-18', 4, 'Impressed with the effectiveness of the training provided. Not much to complain about.'),
(18, '2025-11-29', 1, 'They did not take good care of my baby Luna. She was not fed her meals on time. Will not be taking her back here.'),
(4, '2025-06-21', 2, 'Disappointed with the level of care provided. One of the only positive things I have to say is that my pet came back unharmed.'),
(24, '2026-08-27', 4, 'Great care, would recommend to others who need to leave their pets for a few days'),
(50, '2025-07-09', 3, 'Satisfactory quality of service. Customer service was helpful, but my dog seemed quite tired of the place in only one day of staying there.'),
(32, '2026-01-03', 5, 'If you can, I highly recommend having Hadleigh take care of your pet. She is amazing and kept my girl happy throughout her whole stay.'),
(40, '2025-05-08', 4, 'Amazing service, will definitely bring my pet back in the future.'),
(9, '2025-11-13', 3, 'Leaves something to be desired with the quality of care, but they did everything necessary while I was out of town.'),
(43, '2026-02-09', 3, 'Adequate care for my pet. May or may not be returning in the future.'),
(3, '2026-04-03', 4, 'Great care and customer service! Will be coming back for bathing and nail trimming.'),
(21, '2025-11-02', 2, 'My baby Tiger was left in a cage for hours unattended. This is not what I payed for.');

