DROP TABLE Donation CASCADE CONSTRAINTS;
DROP TABLE DonationType CASCADE CONSTRAINTS;
DROP TABLE Donor CASCADE CONSTRAINTS;
DROP TABLE ReportsTo CASCADE CONSTRAINTS;
DROP TABLE VolunteersFor CASCADE CONSTRAINTS;
DROP TABLE Volunteer CASCADE CONSTRAINTS;
DROP TABLE Contributor CASCADE CONSTRAINTS;
DROP TABLE DeployedFor CASCADE CONSTRAINTS;
DROP TABLE Supplies CASCADE CONSTRAINTS;
DROP TABLE AssistedBy CASCADE CONSTRAINTS;
DROP TABLE Victim CASCADE CONSTRAINTS;
DROP TABLE Shelter CASCADE CONSTRAINTS;
DROP TABLE Mission CASCADE CONSTRAINTS;
DROP TABLE ReliefCenter CASCADE CONSTRAINTS;
DROP TABLE Disaster CASCADE CONSTRAINTS;

-- TODO: make sure to handle these enum values used in test data
-- Supplies Quality: 'Fair' 'Good' 'Exellent' 'New' 
-- Volunteer Status: Active, On Standby, Completed
-- Victim Status: Injured, Missing, Displaced, Homeless
-- date format: YYYY-MM-DD

CREATE TABLE Disaster (
    name VARCHAR(50),
    disasterDate DATE,
    location VARCHAR(100),
    damageCost INTEGER,
    casualties INTEGER,
    severityLevel INTEGER,
    type VARCHAR(50),
    PRIMARY KEY (name, disasterDate, location)
);

CREATE TABLE ReliefCenter (
    name VARCHAR(50), 
    location VARCHAR(100), 
    phoneNumber VARCHAR(15) UNIQUE,
    PRIMARY KEY (name, location)
);

CREATE TABLE Mission ( 
    missionID NUMBER(30),
    missionType VARCHAR(50),
    datePosted DATE, 
    helpNeeded NUMBER(10), 
    disasterName VARCHAR(50) NOT NULL, 
    disasterDate DATE NOT NULL, 
    disasterLocation VARCHAR(100) NOT NULL,
    rcName VARCHAR(50) NOT NULL, 
    rcLocation VARCHAR(100) NOT NULL,
    priority NUMBER(10),
    PRIMARY KEY (missionID),
    FOREIGN KEY (rcName, rcLocation) REFERENCES ReliefCenter(name, location) ON DELETE CASCADE,
    FOREIGN KEY (disasterName, disasterDate, disasterLocation) REFERENCES Disaster(name, disasterDate, location) ON DELETE CASCADE
);

CREATE TABLE Shelter (
    name VARCHAR(50), 
    location VARCHAR(100), 
    capacity INTEGER, 
    currentOccupancy INTEGER, 
    rcName VARCHAR(50), 
    rcLocation VARCHAR(100),
    PRIMARY KEY (name, location),
    FOREIGN KEY (rcName, rcLocation) REFERENCES ReliefCenter(name, location)
);

CREATE TABLE Victim (
    victimID NUMBER(30), 
    name VARCHAR(50), 
    status VARCHAR(250), 
    age NUMBER(30), 
    shelterName VARCHAR(50), 
    shelterLocation VARCHAR(100),
    PRIMARY KEY (victimID),
    FOREIGN KEY (shelterName, shelterLocation) REFERENCES Shelter(name, location)
);

CREATE TABLE AssistedBy (
    victimID NUMBER(30), 
    missionID NUMBER(30), 
    dateAssisted DATE, 
    assistanceType VARCHAR(250),
    PRIMARY KEY (victimID, missionID),
    FOREIGN KEY(victimID) REFERENCES Victim(victimID),
    FOREIGN KEY(missionID) REFERENCES Mission
);

CREATE TABLE Supplies (
    supplyID NUMBER(30), 
    supplyName VARCHAR(50), 
    quantity INTEGER, 
    expirationDate DATE, 
    shelterName VARCHAR(50), 
    shelterLocation VARCHAR(100),
    rcName VARCHAR(50),
    rcLocation VARCHAR(100),
    quality VARCHAR(50), 
    PRIMARY KEY (supplyID),
    FOREIGN KEY (shelterName, shelterLocation) REFERENCES Shelter(name, location),
    FOREIGN KEY (rcName, rcLocation) REFERENCES ReliefCenter(name, location)
);

CREATE TABLE DeployedFor (
    missionID NUMBER(30), 
    supplyID NUMBER(30), 
    amount INTEGER, 
    dateDeployed DATE,
    PRIMARY KEY (missionID, supplyID),
    FOREIGN KEY (missionID) REFERENCES Mission(missionID),
    FOREIGN KEY (supplyID) REFERENCES Supplies(supplyID)
);

CREATE TABLE Contributor (
    name VARCHAR(50), 
    phoneNumber VARCHAR(15), 
    age NUMBER(30), 
    location VARCHAR(100),
    PRIMARY KEY (name, phoneNumber)
);

CREATE TABLE Volunteer (
    name VARCHAR(50), 
    phoneNumber VARCHAR(15),
    locationPreference VARCHAR(100), 
    availability VARCHAR(100),
    PRIMARY KEY (name, phoneNumber),
    FOREIGN KEY (name, phoneNumber) REFERENCES Contributor(name, phoneNumber)
);

CREATE TABLE VolunteersFor (
    missionID NUMBER(30), 
    name VARCHAR(50), 
    phoneNumber VARCHAR(15), 
    role VARCHAR(50), 
    status VARCHAR(250),
    PRIMARY KEY (missionID, name, phoneNumber),
    FOREIGN KEY(name, phoneNumber) REFERENCES Volunteer(name, phoneNumber),
    FOREIGN KEY(missionID) REFERENCES Mission
);

CREATE TABLE ReportsTo (
    rcName VARCHAR(50), 
    rcLocation VARCHAR(100), 
    volunteerName VARCHAR(50), 
    volunteerPhoneNumber VARCHAR(15), 
    dateReported DATE,
    PRIMARY KEY (rcName, rcLocation, volunteerName, volunteerPhoneNumber),
    FOREIGN KEY(volunteerName, volunteerPhoneNumber) REFERENCES Volunteer(name, phoneNumber),
    FOREIGN KEY(rcName, rcLocation) REFERENCES ReliefCenter(name, location)
);

CREATE TABLE Donor (
    name VARCHAR(50), 
    phoneNumber VARCHAR(15), 
    totalDonated INTEGER,
    PRIMARY KEY (name, phoneNumber),
    FOREIGN KEY (name, phoneNumber) REFERENCES Contributor(name, phoneNumber)
);

CREATE TABLE DonationType (
    itemName VARCHAR(50), 
    donationType VARCHAR(50),
    PRIMARY KEY (itemName)
);

CREATE TABLE Donation (
    donationID NUMBER(30), 
    donationAmount INTEGER, 
    dateSent DATE, 
    itemName VARCHAR(50), 
    donorName VARCHAR(50), 
    donorPhoneNumber VARCHAR(15), 
    rcName VARCHAR(50) NOT NULL, 
    rcLocation VARCHAR(100) NOT NULL, 
    dateReceived DATE,
    PRIMARY KEY (donationID, donorName, donorPhoneNumber),
    FOREIGN KEY (donorName, donorPhoneNumber) REFERENCES Donor(name, phoneNumber) ON DELETE CASCADE,
    FOREIGN KEY (rcName, rcLocation) REFERENCES ReliefCenter(name, location) ON DELETE CASCADE,
    FOREIGN KEY (itemName) REFERENCES DonationType(itemName)
);

INSERT INTO ReliefCenter 
VALUES ('Red Cross Center NO', 'New Orleans, LA', '504-123-4567');
INSERT INTO ReliefCenter 
VALUES ('Red Cross Fukushima', 'Fukushima, Japan', '024-987-6543');
INSERT INTO ReliefCenter 
VALUES ('Fire Relief CA', 'Los Angeles, CA', '310-555-1234');
INSERT INTO ReliefCenter 
VALUES ('Disaster Aid Indonesia', 'Jakarta, Indonesia', '021-444-7890');
INSERT INTO ReliefCenter 
VALUES ('Haiti Relief Center', 'Port-au-Prince, Haiti', '509-777-4321');


INSERT INTO Disaster 
VALUES ('Hurricane Katrina', TO_DATE('2005-08-29', 'YYYY-MM-DD'), 'New Orleans, LA', 125000000000, 1836, 9, 'Hurricane');
INSERT INTO Disaster 
VALUES ('Fukushima Earthquake', TO_DATE('2011-03-11', 'YYYY-MM-DD'), 'Fukushima, Japan', 235000000000, 15897, 10, 'Earthquake');
INSERT INTO Disaster 
VALUES ('California Wildfires', TO_DATE('2020-09-01', 'YYYY-MM-DD'), 'California, USA', 12600000000, 31, 6, 'Wildfire');
INSERT INTO Disaster 
VALUES ('Tsunami Indian Ocean', TO_DATE('2004-12-26', 'YYYY-MM-DD'), 'Indonesia', 15000000000, 230000, 10, 'Tsunami');
INSERT INTO Disaster 
VALUES ('Haiti Earthquake', TO_DATE('2010-01-12', 'YYYY-MM-DD'), 'Port-au-Prince, Haiti', 8000000000, 160000, 9, 'Earthquake');

INSERT INTO Mission 
VALUES (1, 'Rescue', TO_DATE('2005-08-30', 'YYYY-MM-DD'), 5000, 'Hurricane Katrina', TO_DATE('2005-08-29', 'YYYY-MM-DD'), 'New Orleans, LA', 'Red Cross Center NO', 'New Orleans, LA', 10);
INSERT INTO Mission 
VALUES (2, 'Evacuation', TO_DATE('2011-03-12', 'YYYY-MM-DD'), 10000, 'Fukushima Earthquake', TO_DATE('2011-03-11', 'YYYY-MM-DD'), 'Fukushima, Japan', 'Red Cross Fukushima', 'Fukushima, Japan', 9);
INSERT INTO Mission 
VALUES (3, 'Firefighting', TO_DATE('2020-09-02', 'YYYY-MM-DD'), 2000, 'California Wildfires', TO_DATE('2020-09-01', 'YYYY-MM-DD'), 'California, USA', 'Fire Relief CA', 'Los Angeles, CA', 7);
INSERT INTO Mission 
VALUES (4, 'Medical Aid', TO_DATE('2004-12-27', 'YYYY-MM-DD'), 7000, 'Tsunami Indian Ocean', TO_DATE('2004-12-26', 'YYYY-MM-DD'), 'Indonesia', 'Disaster Aid Indonesia', 'Jakarta, Indonesia', 8);
INSERT INTO Mission 
VALUES (5, 'Reconstruction', TO_DATE('2010-01-13', 'YYYY-MM-DD'), 3000, 'Haiti Earthquake', TO_DATE('2010-01-12', 'YYYY-MM-DD'), 'Port-au-Prince, Haiti', 'Haiti Relief Center', 'Port-au-Prince, Haiti', 6);

INSERT INTO Shelter 
VALUES ('NO Shelter 1', 'New Orleans, LA', 1000, 950, 'Red Cross Center NO', 'New Orleans, LA');
INSERT INTO Shelter 
VALUES ('Fukushima Shelter 1', 'Fukushima, Japan', 2000, 1800, 'Red Cross Fukushima', 'Fukushima, Japan');
INSERT INTO Shelter 
VALUES ('CA Shelter 1', 'Los Angeles, CA', 500, 450, 'Fire Relief CA', 'Los Angeles, CA');
INSERT INTO Shelter 
VALUES ('Indonesia Shelter 1', 'Jakarta, Indonesia', 1500, 1400, 'Disaster Aid Indonesia', 'Jakarta, Indonesia');
INSERT INTO Shelter 
VALUES ('Haiti Shelter 1', 'Port-au-Prince, Haiti', 800, 750, 'Haiti Relief Center', 'Port-au-Prince, Haiti');

INSERT INTO Victim 
VALUES (1, 'John Doe', 'Injured', 45, 'NO Shelter 1', 'New Orleans, LA');
INSERT INTO Victim 
VALUES (2, 'Jane Smith', 'Missing', 32, 'Fukushima Shelter 1', 'Fukushima, Japan');
INSERT INTO Victim 
VALUES (3, 'Emily White', 'Displaced', 60, 'CA Shelter 1', 'Los Angeles, CA');
INSERT INTO Victim 
VALUES (4, 'Ali Rahman', 'Injured', 27, 'Indonesia Shelter 1', 'Jakarta, Indonesia');
INSERT INTO Victim 
VALUES (5, 'Pierre Louis', 'Homeless', 38, 'Haiti Shelter 1', 'Port-au-Prince, Haiti');

INSERT INTO AssistedBy 
VALUES (1, 1, TO_DATE('2005-08-31', 'YYYY-MM-DD'), 'Medical Assistance');
INSERT INTO AssistedBy 
VALUES (2, 2, TO_DATE('2011-03-13', 'YYYY-MM-DD'), 'Evacuation');
INSERT INTO AssistedBy 
VALUES (3, 3, TO_DATE('2020-09-03', 'YYYY-MM-DD'), 'Fire Relief');
INSERT INTO AssistedBy 
VALUES (4, 4, TO_DATE('2004-12-28', 'YYYY-MM-DD'), 'Medical Treatment');
INSERT INTO AssistedBy 
VALUES (5, 5, TO_DATE('2010-01-14', 'YYYY-MM-DD'), 'Shelter Placement');

INSERT INTO Supplies 
VALUES (1, 'Water Bottles', 5000, TO_DATE('2025-12-31', 'YYYY-MM-DD'), 'NO Shelter 1', 'New Orleans, LA', 'Red Cross Center NO', 'New Orleans, LA', 'Good');
INSERT INTO Supplies 
VALUES (2, 'Blankets', 2000, NULL, 'Fukushima Shelter 1', 'Fukushima, Japan', 'Red Cross Fukushima', 'Fukushima, Japan', 'Excellent');
INSERT INTO Supplies 
VALUES (3, 'Food Packs', 3000, TO_DATE('2023-09-01', 'YYYY-MM-DD'), 'CA Shelter 1', 'Los Angeles, CA', 'Fire Relief CA', 'Los Angeles, CA', 'Fair');
INSERT INTO Supplies 
VALUES (4, 'Medical Kits', 1500, TO_DATE('2024-06-30', 'YYYY-MM-DD'), 'Indonesia Shelter 1', 'Jakarta, Indonesia', 'Fire Relief CA', 'Los Angeles, CA', 'Good');
INSERT INTO Supplies 
VALUES (5, 'Tents', 1000, NULL, 'Haiti Shelter 1', 'Port-au-Prince, Haiti', 'Haiti Relief Center', 'Port-au-Prince, Haiti', 'New');

INSERT INTO DeployedFor VALUES (2, 1, 50, TO_DATE('2023-01-10', 'YYYY-MM-DD'));
INSERT INTO DeployedFor VALUES (2, 2, 100, TO_DATE('2023-02-15', 'YYYY-MM-DD'));
INSERT INTO DeployedFor VALUES (1, 3, 75, TO_DATE('2023-03-20', 'YYYY-MM-DD'));
INSERT INTO DeployedFor VALUES (1, 4, 120, TO_DATE('2023-04-25', 'YYYY-MM-DD'));
INSERT INTO DeployedFor VALUES (3, 5, 90, TO_DATE('2023-05-30', 'YYYY-MM-DD'));

INSERT INTO Contributor 
VALUES ('Alice Johnson', '555-111-2222', 35, 'New York, USA');
INSERT INTO Contributor 
VALUES ('Bob Miller', '555-333-4444', 42, 'Tokyo, Japan');
INSERT INTO Contributor 
VALUES ('Catherine Green', '555-555-6666', 29, 'Los Angeles, CA');
INSERT INTO Contributor 
VALUES ('Daniel Brown', '555-777-8888', 50, 'Jakarta, Indonesia');
INSERT INTO Contributor 
VALUES ('Emily Davis', '555-999-0000', 27, 'Haiti');
INSERT INTO Contributor 
VALUES ('John Smith', '555-123-4567', 0, 'New Orleans, LA');
INSERT INTO Contributor 
VALUES ('Emily Johnson', '555-987-6543', 0, 'Fukushima, Japan');
INSERT INTO Contributor 
VALUES ('Michael Brown', '555-456-7890', 0, 'Los Angeles, CA');
INSERT INTO Contributor 
VALUES ('Sarah Williams', '555-321-0987', 0, 'Jakarta, Indonesia');
INSERT INTO Contributor 
VALUES ('David Wilson', '555-654-3210', 0, 'Port-au-Prince, Haiti');

INSERT INTO Volunteer VALUES ('John Smith', '555-123-4567', 'New Orleans, LA', 'Weekends');
INSERT INTO Volunteer VALUES ('Emily Johnson', '555-987-6543', 'Fukushima, Japan', 'Full-Time');
INSERT INTO Volunteer VALUES ('Michael Brown', '555-456-7890', 'Los Angeles, CA', 'Weekdays');
INSERT INTO Volunteer VALUES ('Sarah Williams', '555-321-0987', 'Jakarta, Indonesia', 'On-Call');
INSERT INTO Volunteer VALUES ('David Wilson', '555-654-3210', 'Port-au-Prince, Haiti', 'Evenings');

INSERT INTO Donor VALUES ('Alice Johnson', '555-111-2222', 5000);
INSERT INTO Donor VALUES ('Bob Miller', '555-333-4444', 10000);
INSERT INTO Donor VALUES ('Catherine Green', '555-555-6666', 7500);
INSERT INTO Donor VALUES ('Daniel Brown', '555-777-8888', 2000);
INSERT INTO Donor VALUES ('Emily Davis', '555-999-0000', 1500);

INSERT INTO DonationType VALUES ('Water Bottles', 'Essential Supplies');
INSERT INTO DonationType VALUES ('Blankets', 'Shelter Aid');
INSERT INTO DonationType VALUES ('Food Packs', 'Essential Supplies');
INSERT INTO DonationType VALUES ('Medical Kits', 'Healthcare Supplies');
INSERT INTO DonationType VALUES ('Tents', 'Shelter Aid');

INSERT INTO Donation 
VALUES (1, 500, TO_DATE('2023-01-15', 'YYYY-MM-DD'), 'Water Bottles', 'Alice Johnson', '555-111-2222', 'Red Cross Center NO', 'New Orleans, LA', TO_DATE('2023-01-20', 'YYYY-MM-DD'));
INSERT INTO Donation 
VALUES (2, 200, TO_DATE('2023-02-10', 'YYYY-MM-DD'), 'Blankets', 'Bob Miller', '555-333-4444', 'Red Cross Fukushima', 'Fukushima, Japan', TO_DATE('2023-02-15', 'YYYY-MM-DD'));
INSERT INTO Donation 
VALUES (3, 1000, TO_DATE('2023-03-05', 'YYYY-MM-DD'), 'Food Packs', 'Catherine Green', '555-555-6666', 'Fire Relief CA', 'Los Angeles, CA', TO_DATE('2023-03-10', 'YYYY-MM-DD'));
INSERT INTO Donation 
VALUES (4, 300, TO_DATE('2023-04-12', 'YYYY-MM-DD'), 'Medical Kits', 'Daniel Brown', '555-777-8888', 'Disaster Aid Indonesia', 'Jakarta, Indonesia', TO_DATE('2023-04-18', 'YYYY-MM-DD'));
INSERT INTO Donation 
VALUES (5, 400, TO_DATE('2023-05-20', 'YYYY-MM-DD'), 'Tents', 'Emily Davis', '555-999-0000', 'Haiti Relief Center', 'Port-au-Prince, Haiti', TO_DATE('2023-05-25', 'YYYY-MM-DD'));

INSERT INTO VolunteersFor 
VALUES (1, 'John Smith', '555-123-4567', 'Medical Aid', 'Active');
INSERT INTO VolunteersFor 
VALUES (2, 'Emily Johnson', '555-987-6543', 'Logistics Coordinator', 'Active');
INSERT INTO VolunteersFor 
VALUES (3, 'Michael Brown', '555-456-7890', 'Rescue Worker', 'On Standby');
INSERT INTO VolunteersFor 
VALUES (4, 'Sarah Williams', '555-321-0987', 'Food Distribution', 'Active');
INSERT INTO VolunteersFor 
VALUES (5, 'David Wilson', '555-654-3210', 'Shelter Management', 'Completed');

INSERT INTO ReportsTo 
VALUES ('Red Cross Center NO', 'New Orleans, LA', 'John Smith', '555-123-4567', TO_DATE('2023-01-12', 'YYYY-MM-DD'));
INSERT INTO ReportsTo 
VALUES ('Red Cross Fukushima', 'Fukushima, Japan', 'Emily Johnson', '555-987-6543', TO_DATE('2023-02-18', 'YYYY-MM-DD'));
INSERT INTO ReportsTo 
VALUES ('Fire Relief CA', 'Los Angeles, CA', 'Michael Brown', '555-456-7890', TO_DATE('2023-03-22', 'YYYY-MM-DD'));
INSERT INTO ReportsTo 
VALUES ('Disaster Aid Indonesia', 'Jakarta, Indonesia', 'Sarah Williams', '555-321-0987', TO_DATE('2023-04-28', 'YYYY-MM-DD'));
INSERT INTO ReportsTo 
VALUES ('Haiti Relief Center', 'Port-au-Prince, Haiti', 'David Wilson', '555-654-3210', TO_DATE('2023-05-30', 'YYYY-MM-DD'));
