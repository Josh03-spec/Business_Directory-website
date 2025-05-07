-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 30, 2025 at 05:51 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uganda_connect`
--

-- --------------------------------------------------------

--
-- Table structure for table `businesses`
--

DROP TABLE IF EXISTS `businesses`;
CREATE TABLE IF NOT EXISTS `businesses` (
  `business_id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `contact_phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_approved` tinyint(1) DEFAULT '0',
  `avg_rating` decimal(3,2) DEFAULT '0.00' COMMENT 'Stores average rating calculated from reviews',
  PRIMARY KEY (`business_id`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `businesses`
--

INSERT INTO `businesses` (`business_id`, `category_id`, `user_id`, `name`, `description`, `contact_phone`, `address`, `website`, `created_at`, `updated_at`, `is_approved`, `avg_rating`) VALUES
(10, 46, 1, 'Uganda Martyrs University', 'The catholic Premier University in Uganda', '0750131937', 'P.O. Box 5498, Kampala, Uganda', 'https://umu.ac.ug/', '2025-03-24 07:49:18', '2025-03-24 09:19:01', 1, 0.00),
(2, 27, 1, 'Milima Technologies', 'Cyber Security Company', '0778290491', 'Plot 650 Kiwatule Kazinga', 'https://www.milimasecurity.com', '2025-03-04 06:28:37', '2025-03-04 06:28:37', 1, 0.00),
(3, 1, 2, 'John\'s Plumbing', 'Expert plumbing services', '123-456-7890', '123 Main St', 'http://johnsplumbing.com', '2025-03-11 21:08:53', '2025-03-11 21:08:53', 1, 0.00),
(4, 2, 2, 'Electricians R Us', 'Professional electrical services', '987-654-3210', '456 Elm St', 'http://electriciansrus.com', '2025-03-11 21:08:53', '2025-03-11 21:08:53', 1, 0.00),
(5, 46, NULL, 'Makerere Business School', 'Makerere University Business School (MUBS) is a public university in Uganda that offers business and management education at various levels, including certificates, diplomas, undergraduate, and postgraduate programs. Established in 1997, it is located in Nakawa, Kampala, and is affiliated with Makerere University, the oldest university in Uganda.', ' +256-414-505-921', 'Plot 21A Port Bell Road, in Nakawa Division,Kampala', 'https://mubs.ac.ug/', '2025-03-11 21:33:29', '2025-03-18 07:41:19', 1, 0.00),
(6, 11, NULL, 'RentoKill', 'Rentokil has been protecting businesses and homes in Uganda from pests for over 60 years. Trust us, the market leaders with a combined global experience of 90 years to give you expert advice and quality pest control solutions tailored to your specific needs. ', '0414287160', 'Bukoto II, Kampala, Uganda', 'https://www.rentokil.co.ug', '2025-03-18 07:18:29', '2025-03-18 07:40:40', 1, 0.00),
(7, 34, NULL, 'Summit Service Center', 'Auto Repair Shop at Kayabwe Mpigi Uganda', '0757839407', 'Kayabwe Mpigi Uganda', '', '2025-03-18 07:49:26', '2025-03-18 07:51:23', 1, 0.00),
(12, 18, 2, 'Nkozi Hospital', 'Hospital In NKozi Mpigi Uganda', '+256 789 661917', 'Buseese, Mawokota, Uganda', 'http://www.nkozihospital.org/', '2025-03-26 06:59:44', '2025-03-26 07:02:15', 1, 0.00),
(9, 9, 2, 'Kayabwe Unisex Salon', 'A hairdressing and Salon for Men and Women \r\nManicure and Pedicure can also be done.', '0750131937', 'Kayabwe Mpigi Uganda', '', '2025-03-18 09:10:13', '2025-03-18 09:11:02', 1, 0.00),
(14, 23, 42, 'TechSolutions Inc.', 'Provides innovative software solutions for businesses.', '555-123-4567', '123 Innovation Ave, Tech City, CA 90210', 'www.techsolutions.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(15, 12, 15, 'Cozy Cafe', 'A warm and inviting cafe serving coffee, pastries, and light meals.', '555-987-6543', '456 Main St, Anytown, NY 10001', 'www.cozycafe.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(16, 34, 28, 'Green Gardens Landscaping', 'Offers professional landscaping and gardening services.', '555-246-8012', '789 Oak Ln, Suburbia, IL 60007', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(17, 5, 8, 'City Bookstore', 'A wide selection of new and used books, plus author events.', '555-135-7911', '101 Bookworm Rd, Litville, MA 02134', 'www.citybookstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(18, 41, 39, 'Auto Repair Experts', 'Provides comprehensive auto repair and maintenance services.', '555-864-2000', '234 Mechanic St, Motor City, MI 48220', NULL, '2025-03-27 13:22:55', '2025-03-27 13:25:13', 1, 0.00),
(19, 18, 22, 'Fashion Forward Boutique', 'Offers the latest trends in women\'s clothing and accessories.', '555-753-1597', '567 Style Ave, Trendtown, TX 77002', 'www.fashionforward.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(20, 29, 33, 'Healthy Harvest Market', 'A grocery store specializing in organic and locally sourced produce.', '555-321-7654', '890 Farm Rd, Greentown, OR 97201', 'www.healthyharvest.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(21, 1, 4, 'Pet Grooming Paradise', 'Provides professional grooming services for dogs and cats.', '555-654-3210', '912 Pet St, Animal City, WA 98109', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(22, 14, 19, 'Home Improvement Center', 'Offers a wide range of hardware, tools, and home improvement supplies.', '555-214-5879', '345 Fixit Ln, Handyville, PA 19104', 'www.homeimprovementcenter.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(23, 38, 45, 'Dental Care Clinic', 'Provides comprehensive dental services for the whole family.', '555-876-9012', '678 Smile Dr, Dental City, NJ 07030', 'www.dentalcareclinic.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(24, 3, 7, 'Music Makers Studio', 'Offers music lessons for various instruments and skill levels.', '555-987-6541', '246 Harmony Rd, Melodyville, GA 30303', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(25, 27, 31, 'The Art Gallery', 'Exhibits and sells works by local and international artists.', '555-741-8520', '580 Art St, Creative City, CO 80202', 'www.theartgallery.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(26, 10, 13, 'Travel Experts Agency', 'Provides travel planning and booking services for individuals and groups.', '555-528-9631', '135 Wanderlust Way, Travel Town, FL 33101', 'www.travelexperts.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(27, 44, 4, 'Legal Aid Services', 'Offers legal assistance and representation for various legal matters.', '555-369-1470', '901 Justice Ave, Lawtown, MD 21201', NULL, '2025-03-27 13:22:55', '2025-03-27 13:25:15', 1, 0.00),
(28, 17, 20, 'Fitness First Gym', 'A state-of-the-art fitness center with personal training and group classes.', '555-632-5896', '428 Muscle Blvd, Fit City, AZ 85001', 'www.fitnessfirst.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(29, 32, 36, 'Event Planning Pros', 'Provides full-service event planning and coordination.', '555-159-7530', '713 Party Pl, Celebration, NV 89109', 'www.eventplanningpros.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(30, 8, 11, 'Kids Learning Center', 'Offers childcare and early education programs.', '555-258-9631', '357 School St, Educity, KS 66045', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(31, 21, 25, 'Photography Studio', 'Provides professional photography services for portraits, events, and commercial needs.', '555-456-1239', '682 Shutter Ln, Imageville, NM 87102', 'www.photostudio.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(32, 37, 43, 'Skin Care Solutions', 'Offers a range of skincare products and services.', '555-789-9876', '925 Beauty Rd, Glowtown, OK 73101', 'www.skincaresolutions.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(33, 2, 6, 'Seafood Restaurant', 'Serves fresh seafood dishes in a fine dining atmosphere.', '555-369-2581', '147 Ocean Ave, Coast City, RI 02889', 'www.seafoodrestaurant.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(34, 16, 2, 'Furniture Gallery', 'Offers a wide selection of home furniture.', '555-147-8523', '258 Design Dr, Style City, SD 57101', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(35, 26, 30, 'Dance Studio', 'Provides dance lessons for all ages and skill levels.', '555-258-7419', '593 Rhythm Rd, Dance Town, TN 37203', 'www.dancestudio.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(36, 9, 12, 'Tutoring Services', 'Offers academic tutoring for students of all ages.', '555-698-3214', '864 Study St, Learntown, UT 84101', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(37, 43, 46, 'Veterinary Clinic', 'Provides medical care for pets.', '555-963-1478', '103 Animal Ave, Petville, VT 05401', 'www.veterinaryclinic.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(38, 19, 23, 'Sports Equipment Store', 'Sells sporting goods and equipment.', '555-852-3697', '369 Game Pl, Sport City, VA 23220', 'www.sportsequipment.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(39, 33, 37, 'Catering Services', 'Provides catering for events of all sizes.', '555-741-2580', '621 Feast St, Banquet Town, WV 25301', 'www.cateringservices.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(40, 7, 10, 'Language School', 'Offers language courses for various languages.', '555-582-9631', '958 Speak St, Language City, WI 53202', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(41, 20, 24, 'Real Estate Agency', 'Provides real estate services for buying, selling, and renting.', '555-415-8529', '124 Home Rd, Property Town, WY 82001', 'www.realestateagency.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(42, 36, 40, 'Spa and Salon', 'Offers a variety of spa and salon services.', '555-287-9635', '487 Relax Ave, Spa City, AL 35203', 'www.spaandsalon.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(43, 1, 3, 'Aquarium Shop', 'Sells fish, aquatic plants, and aquarium supplies.', '555-693-2581', '751 Ocean Blvd, Fish Town, AK 99501', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(44, 11, 14, 'Used Car Dealership', 'Sells pre-owned vehicles.', '555-326-1479', '185 Auto Mall Dr, Car City, AR 72201', 'www.usedcars.com', '2025-03-27 13:22:55', '2025-03-27 13:25:17', 1, 0.00),
(45, 24, 28, 'Clothing Store', 'Offers a variety of clothing for men, women, and children.', '555-985-2147', '528 Fashion Way, Style Town, CT 06106', 'www.clothingstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(46, 40, 44, 'Urgent Care Clinic', 'Provides immediate medical care for non-life-threatening conditions.', '555-741-5896', '891 Health Pl, Med City, DE 19901', 'www.urgentcareclinic.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(47, 13, 17, 'Electronics Store', 'Sells a variety of electronic devices and accessories.', '555-214-8520', '214 Tech Rd, Gadget Town, HI 96813', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(48, 28, 32, 'Dance Club', 'Offers a place to dance and socialize.', '555-587-9632', '587 Groove St, Night City, ID 83702', 'www.danceclub.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(49, 4, 5, 'Pizza Place', 'Serves pizza, pasta, and other Italian dishes.', '555-123-9874', '963 Slice Ave, Pizza Town, IN 46204', 'www.pizzaplace.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(50, 15, 18, 'Jewelry Store', 'Sells a variety of jewelry items.', '555-456-7891', '741 Sparkle Ln, Gem City, IA 50309', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(51, 31, 35, 'Moving Company', 'Provides moving services for residential and commercial clients.', '555-879-6542', '102 Move St, Transit Town, KY 40202', 'www.movingcompany.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(52, 6, 9, 'Ice Cream Shop', 'Serves ice cream and other frozen treats.', '555-236-9871', '369 Cone Ct, Sweet City, LA 70801', 'www.icecreamshop.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(53, 22, 26, 'Record Store', 'Sells vinyl records, CDs, and other music merchandise.', '555-598-7410', '632 Music Ln, Sound Town, ME 04101', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(54, 39, 41, 'Therapy Center', 'Provides mental health therapy and counseling services.', '555-963-2584', '258 Hope Rd, Mind City, MA 02116', 'www.therapycenter.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(55, 18, 21, 'Bookstore', 'Sells new and used books.', '555-753-6981', '591 Page St, Read Town, MI 48207', 'www.bookstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(56, 30, 34, 'Art Supply Store', 'Sells art supplies and materials.', '555-125-7896', '824 Craft Ave, Art City, MN 55401', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(57, 5, 7, 'Coffee Shop', 'Serves coffee, tea, and pastries.', '555-698-4123', '147 Brew St, Java Town, MS 39201', 'www.coffeeshop.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(58, 17, 20, 'Clothing Boutique', 'Offers trendy clothing and accessories.', '555-369-8521', '485 Style Ln, Chic City, MO 63101', 'www.boutique.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(59, 32, 36, 'Event Venue', 'Provides a space for hosting events.', '555-852-1479', '712 Party Rd, Event Town, MT 59101', 'www.eventvenue.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(60, 10, 13, 'Tutoring Center', 'Offers tutoring services for students.', '555-214-5896', '945 Learn St, Study City, NE 68508', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(61, 23, 27, 'Software Company', 'Develops and sells software applications.', '555-587-2140', '112 Code Ave, Tech Town, NV 89101', 'www.software.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(62, 38, 42, 'Chiropractor Clinic', 'Provides chiropractic services.', '555-951-7893', '389 Spine Rd, Adjust City, NH 03101', 'www.chiropractor.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(63, 2, 4, 'Italian Restaurant', 'Serves Italian cuisine.', '555-624-8527', '652 Pasta Pl, Italy Town, NJ 07102', 'www.italianrestaurant.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(64, 14, 16, 'Hardware Store', 'Sells tools and hardware supplies.', '555-397-4158', '287 Fixit St, Handy Town, NM 87105', NULL, '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(65, 25, 29, 'Theater Company', 'Produces and performs theatrical productions.', '555-760-1234', '520 Stage Rd, Drama City, NY 10036', 'www.theater.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(66, 41, 45, 'Physical Therapy', 'Provides physical therapy services.', '555-136-9870', '893 Move Ln, Rehab Town, NC 27601', 'www.physicaltherapy.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(67, 1, 1, 'Bookstore', 'Sells books', '555-879-4561', '100 Main St', 'bookstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(68, 3, 2, 'Music Store', 'Sells musical instruments', '555-987-6543', '200 Main St', 'musicstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(69, 5, 3, 'Coffee Shop', 'Serves coffee', '555-123-4567', '300 Main St', 'coffeeshop.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(70, 7, 4, 'Restaurant', 'Serves food', '555-456-7890', '400 Main St', 'restaurant.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(71, 9, 5, 'Bar', 'Serves drinks', '555-789-1234', '500 Main St', 'bar.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(72, 11, 6, 'Gym', 'Provides fitness services', '555-234-5678', '600 Main St', 'gym.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(73, 13, 7, 'Spa', 'Provides relaxation services', '555-567-8901', '700 Main St', 'spa.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(74, 15, 8, 'Salon', 'Provides hair services', '555-890-1234', '800 Main St', 'salon.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(75, 17, 9, 'Clinic', 'Provides medical services', '555-123-4567', '900 Main St', 'clinic.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(76, 19, 10, 'Hospital', 'Provides hospital services', '555-456-7890', '1000 Main St', 'hospital.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(77, 21, 11, 'School', 'Provides education services', '555-789-1234', '1100 Main St', 'school.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(78, 23, 12, 'University', 'Provides higher education', '555-234-5678', '1200 Main St', 'university.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(79, 25, 13, 'Library', 'Provides books and resources', '555-567-8901', '1300 Main St', 'library.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(80, 27, 14, 'Museum', 'Provides exhibits', '555-890-1234', '1400 Main St', 'museum.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(81, 29, 15, 'Theater', 'Provides performances', '555-123-4567', '1500 Main St', 'theater.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(82, 31, 16, 'Park', 'Provides outdoor recreation', '555-456-7890', '1600 Main St', 'park.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(84, 35, 18, 'Aquarium', 'Provides aquatic exhibits', '555-234-5678', '1800 Main St', 'aquarium.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(85, 37, 19, 'Garden', 'Provides plant exhibits', '555-567-8901', '1900 Main St', 'garden.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(86, 39, 20, 'Farm', 'Provides agricultural exhibits', '555-890-1234', '2000 Main St', 'farm.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(87, 22, 21, 'Grocery Store', 'Sells groceries', '555-111-2222', '2100 Food St', 'grocerystore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(88, 24, 22, 'Bakery', 'Sells baked goods', '555-333-4444', '2200 Bread St', 'bakery.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(89, 26, 23, 'Butcher Shop', 'Sells meat', '555-555-6666', '2300 Meat St', 'butchershop.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(90, 28, 24, 'Fish Market', 'Sells fish', '555-777-8888', '2400 Fish St', 'fishmarket.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(91, 30, 25, 'Deli', 'Sells deli meats and cheeses', '555-999-0000', '2500 Deli St', 'deli.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(92, 34, 26, 'Clothing Store', 'Sells clothes', '555-246-8024', '2600 Style St', 'clothingstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(93, 36, 27, 'Shoe Store', 'Sells shoes', '555-135-7935', '2700 Shoe St', 'shoestore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(94, 38, 28, 'Hat Store', 'Sells hats', '555-864-2046', '2800 Hat St', 'hatstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(95, 40, 29, 'Bag Store', 'Sells bags', '555-753-1597', '2900 Bag St', 'bagstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(96, 42, 30, 'Jewelry Store', 'Sells jewelry', '555-321-7654', '3000 Sparkle St', 'jewelrystore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(97, 1, 31, 'Electronics Store', 'Sells electronics', '555-654-3210', '3100 Tech St', 'electronicsstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(98, 3, 32, 'Computer Store', 'Sells computers', '555-214-5879', '3200 Byte St', 'computerstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(99, 5, 33, 'Phone Store', 'Sells phones', '555-876-9012', '3300 Call St', 'phonestore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(100, 7, 34, 'TV Store', 'Sells TVs', '555-987-6541', '3400 Screen St', 'tvstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(101, 9, 35, 'Camera Store', 'Sells cameras', '555-741-8520', '3500 Lens St', 'camerastore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(102, 11, 36, 'Furniture Store', 'Sells furniture', '555-528-9631', '3600 Home St', 'furniturestore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(103, 13, 37, 'Appliance Store', 'Sells appliances', '555-369-1470', '3700 Fix St', 'appliancestore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(104, 15, 38, 'Garden Store', 'Sells garden supplies', '555-632-5896', '3800 Green St', 'gardenstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(105, 17, 39, 'Tool Store', 'Sells tools', '555-159-7530', '3900 Tool St', 'toolstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(106, 19, 40, 'Paint Store', 'Sells paint', '555-258-9631', '4000 Color St', 'paintstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(107, 21, 41, 'Toy Store', 'Sells toys', '555-456-1239', '4100 Play St', 'toystore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(108, 23, 42, 'Game Store', 'Sells games', '555-789-9876', '4200 Fun St', 'gamestore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(109, 25, 43, 'Sporting Goods Store', 'Sells sporting goods', '555-369-2581', '4300 Sport St', 'sportinggoodsstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(110, 27, 44, 'Bike Store', 'Sells bikes', '555-147-8523', '4400 Ride St', 'bikestore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(111, 29, 45, 'Outdoor Store', 'Sells outdoor gear', '555-258-7419', '4500 Camp St', 'outdoorstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(112, 31, 46, 'Pet Store', 'Sells pet supplies', '555-698-3214', '4600 Paw St', 'petstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(113, 33, 1, 'Pharmacy', 'Sells medicine', '555-963-1478', '4700 Health St', 'pharmacy.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(114, 35, 2, 'Bookstore', 'Sells books', '555-852-3697', '4800 Page St', 'bookstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(115, 37, 3, 'Clothing Store', 'Sells clothes', '555-741-2580', '4900 Style St', 'clothingstore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(116, 39, 4, 'Grocery Store', 'Sells groceries', '555-582-9631', '5000 Food St', 'grocerystore.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(117, 41, 5, 'Bank', 'Provides financial services', '555-415-8529', '5100 Money St', 'bank.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(118, 43, 6, 'Post Office', 'Provides postal services', '555-287-9635', '5200 Mail St', 'postoffice.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 1, 0.00),
(119, 2, 7, 'Law Firm', 'Provides legal services', '555-693-2581', '5300 Law St', 'lawfirm.com', '2025-03-27 13:22:55', '2025-03-27 13:25:21', 1, 0.00),
(120, 4, 8, 'Accounting Firm', 'Provides accounting services', '555-326-1479', '5400 Tax St', 'accountingfirm.com', '2025-03-27 13:22:55', '2025-03-28 12:40:36', 1, 0.00),
(121, 6, 9, 'Consulting Firm', 'Provides consulting services', '555-985-2147', '5500 Advice St', 'consultingfirm.com', '2025-03-27 13:22:55', '2025-03-28 12:40:47', 1, 0.00),
(123, 10, 11, 'Real Estate Agency', 'Provides real estate services', '555-214-8520', '5700 Home St', 'realestateagency.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 0, 0.00),
(124, 12, 12, 'Advertising Agency', 'Provides advertising services', '555-587-9632', '5800 Ad St', 'advertisingagency.com', '2025-03-27 13:22:55', '2025-04-07 10:21:30', 1, 0.00),
(126, 16, 14, 'PR Agency', 'Provides public relations services', '555-456-7891', '6000 Media St', 'pragency.com', '2025-03-27 13:22:55', '2025-03-28 12:40:23', 1, 0.00),
(128, 20, 16, 'IT Department', 'Provides information technology services', '555-236-9871', '6200 Code St', 'itdepartment.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 0, 0.00),
(130, 24, 18, 'Sales Department', 'Handles sales', '555-963-2584', '6400 Sell St', 'salesdepartment.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 0, 0.00),
(131, 26, 19, 'Customer Service', 'Handles customer support', '555-753-6981', '6500 Help St', 'customerservice.com', '2025-03-27 13:22:55', '2025-03-27 13:25:24', 1, 0.00),
(132, 28, 20, 'Research Department', 'Conducts research', '555-125-7896', '6600 Study St', 'researchdepartment.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 0, 0.00),
(134, 32, 22, 'Quality Assurance', 'Ensures quality', '555-369-8521', '6800 Test St', 'qualityassurance.com', '2025-03-27 13:22:55', '2025-03-27 13:22:55', 0, 0.00),
(136, 36, 24, 'Marketing Department', 'Handles marketing', '555-214-5896', '7000 Market St', 'marketingdepartment.com', '2025-03-27 13:22:55', '2025-03-28 12:45:06', 1, 0.00),
(139, 1, 2, 'Moses Plumbing', 'We do all kinds of Plumbing in different situations On site,in buildings, repairs', '88475565465', 'Uganda Business District Plot 77', 'https://mosesplumbing.org', '2025-03-28 09:08:05', '2025-03-28 12:23:36', 1, 0.00),
(140, 1, 2, 'Fire Suppression Limited', 'Fire Department and rescue services', '45623456543', 'Kabale Main Street', NULL, '2025-04-15 08:08:05', '2025-04-15 12:23:36', 1, 5.00),
(141, 9, 2, 'Beauty World Salon and Bridals', '\"Professional and good customer care\".\r\nWe deal in all types of Hairstyles and Makeup on Discount.\r\nFree hair Washing and ice cream for all University Students.', '0781178650, 07530144', 'Nkozi TC', '', '2025-04-27 12:04:35', '2025-04-27 12:16:58', 1, 0.00),
(142, 19, 2, 'Tororo Science Supplies', 'Dealers in Laboratory Chemicals and Equipment Formalin(38-40%) for treating dead bodies also available.\r\nEmail:vicentebusene@gmail.com', '0772610065, 07576478', 'Nkozi TC', '', '2025-04-27 12:15:29', '2025-04-27 12:16:54', 1, 0.00),
(143, 48, 2, 'Cente Agent', 'Financial Services Agent \r\nCentenary Bank, Stanbic Bank, MTN Mobile Money , Airtel Money ', '0778648487, 07066484', 'Nkozi TC', '', '2025-04-27 12:35:43', '2025-04-27 13:10:14', 1, 0.00),
(144, 37, 2, 'Kololo Desire Suites Nkozi', 'Self Contained Rooms for Rent', '0754342508, 07543602', 'Nkozi TC', '', '2025-04-27 13:05:19', '2025-04-27 13:10:11', 1, 0.00),
(145, 37, 2, 'Leo Lwanga Hostel', 'Hostels', '0756347274', 'Nkozi TC', '', '2025-04-27 13:09:26', '2025-04-27 13:10:08', 1, 0.00),
(146, 37, 2, 'Wamala Hostel', 'Men\'s Hostel just outside Uganda Martyrs University.\r\nNear Nkozi Hospital', '0750131937', 'Nkozi ', '', '2025-04-27 13:14:18', '2025-04-27 13:20:13', 1, 0.00),
(148, 22, 2, 'Hass Supermarket', 'Retail Supermarket', '0755519088, 07572379', 'Nkozi TC', '', '2025-04-27 13:16:58', '2025-04-27 13:20:07', 1, 0.00),
(150, 20, 2, 'Maama Mukadde Restaurant', 'Restaurant at Nkozi TC.\r\nServes Breakfast Lunch and Dinner', '0759393528, 07776877', 'Nkozi TC', '', '2025-04-27 13:21:33', '2025-04-27 13:22:03', 1, 0.00),
(162, 37, 2, 'St. Mugaga Hostel', 'Ladies\'s Hostel just outside Uganda Martyrs University.\r\nNear Nkozi Hospital', '0750131937', 'Nkozi ', '', '2025-04-28 12:59:49', '2025-04-28 12:59:49', 0, 0.00),
(151, 20, 2, 'Express Chips and Chicken', 'Sells Chips and Chicken around Nkozi.', '0706712990, 07794295', 'Nkozi TC', '', '2025-04-27 13:24:38', '2025-04-27 14:18:44', 1, 0.00),
(152, 19, 2, 'St. Francis Life Care Drug Shop', 'Drug Shop At Nkozi TC', '0752389850, 07794295', 'Nkozi TC', '', '2025-04-27 13:27:22', '2025-04-27 14:19:28', 1, 0.00),
(153, 13, 2, 'Mulangira Electronics and Sounds', 'Sells Electronics, Installs Decoders DSTV and Gotv, \r\nRepairs Appliances', '0752006942', 'Nkozi TC', '', '2025-04-27 13:33:54', '2025-04-27 14:19:24', 1, 0.00),
(154, 13, 2, 'Family Hardware and Electronics Nkozi', 'Deals in Hardware and Electronics Installation around Nkozi.\r\nAlso does repair', '0705792161, 07526848', 'Nkozi TC', '', '2025-04-27 13:36:16', '2025-04-27 14:19:21', 1, 0.00),
(155, 13, 2, 'Rama Electronics and phone Accessories Center Nkozi', 'Dealers in all kinds of Electronics, mobile phones, batteries, chargers etc', '0759473146, 07807113', 'Nkozi TC', '', '2025-04-27 13:39:51', '2025-04-27 14:19:17', 1, 0.00),
(156, 49, 2, 'AKII STARR fashions and sports centre Nkozi', 'Deals in jerseys and sports wear and Gear', '0750131937', 'Nkozi TC', '', '2025-04-27 13:48:48', '2025-04-27 14:19:13', 1, 0.00),
(157, 22, 2, 'Taata Breaget Traders', 'Deals in Springs Drinking Water (Depot) and sells general merchandise.', '0753674717', 'Nkozi TC', '', '2025-04-27 13:53:38', '2025-04-27 14:19:07', 1, 0.00),
(158, 50, 2, 'Glory Bookshop And Gift Centre', 'For all your Stationary, Gift Cards, Kids\' toys, Ceremonials Cakes, Decorations, Introduction Items and General Merchandise.', '0782691348, 07015744', 'Nkozi TC', '', '2025-04-27 14:02:47', '2025-04-27 14:19:10', 1, 0.00),
(159, 22, 2, 'Mugewra General Traders ', 'Deals in trading General  Merchandise.\r\nICE Springs Water Depot', '075854247', 'Nkozi TC', '', '2025-04-27 14:06:30', '2025-04-27 14:19:01', 1, 0.00),
(160, 9, 2, 'SLIV HAIR Centre and Salon', 'Dealers in Wigs, Human Hair, Waves, Hair Extension and Crochets.', '0757532677, 07822906', 'Nkozi TC', '', '2025-04-27 14:12:54', '2025-04-27 14:18:57', 1, 0.00),
(161, 20, 2, 'SILENT EATS LIMITED', 'Cookies, G.nuts, Hardcorns, Daddies, Soya, Crisps, Mandazi,Chapati,Sumbusa,Kebabs, Egg rolls, Cakes', '0750131937', 'Nkozi TC', '', '2025-04-27 14:18:11', '2025-04-27 14:18:54', 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `status`) VALUES
(1, 'Plumbing', 1),
(2, 'Electricians', 1),
(3, 'Mechanics (Auto)', 1),
(4, 'Carpenters', 1),
(5, 'Builders/Construction', 1),
(6, 'Painters', 1),
(7, 'Welders', 1),
(8, 'Tailors/Seamstresses', 1),
(9, 'Hairdressers/Barbers', 1),
(10, 'Cleaning Services', 1),
(11, 'Pest Control', 1),
(12, 'Computer Repair/IT Services', 1),
(13, 'Household Electronics and Appliances', 1),
(14, 'Schools (Primary)', 1),
(15, 'Schools (Secondary)', 1),
(16, 'Tutoring Services', 1),
(17, 'Doctors', 1),
(18, 'Clinics/Hospitals', 1),
(19, 'Pharmacies', 1),
(20, 'Restaurants/Eateries', 1),
(21, 'Wholesale Markets (Agricultural Produce)', 1),
(22, 'Retail Markets', 1),
(23, 'Food Delivery Services', 1),
(24, 'Farms/Agricultural Suppliers', 1),
(25, 'Lawyers', 1),
(26, 'Accountants', 1),
(27, 'Consultants', 1),
(28, 'Marketing/Advertising', 1),
(29, 'Web Design/Development', 1),
(30, 'Graphic Design', 1),
(31, 'Printing Services', 1),
(32, 'Car Dealers', 1),
(33, 'Motorcycle Dealers', 1),
(34, 'Auto Repair Shops', 1),
(35, 'Spare Parts Suppliers', 1),
(36, 'Real Estate Agents', 1),
(37, 'Hostels', 1),
(38, 'Land Sales', 1),
(39, 'Event Planners', 1),
(40, 'Photographers/Videographers', 1),
(41, 'Transportation (Taxi)', 1),
(42, 'Transportation (Boda-boda)', 1),
(43, 'Security Services', 1),
(44, 'Domestic Workers (Nannies)', 1),
(45, 'Arts and Crafts', 1),
(46, 'Tertiary Institutions', 1),
(48, 'Financial Services', 1),
(49, 'Clothes and Boutique Shops', 1),
(50, 'Stationary', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `business_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `review_text` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `business_id` (`business_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `business_id`, `user_id`, `rating`, `review_text`, `created_at`) VALUES
(1, 1, 3, 5, 'Excellent service!', '2025-03-11 21:10:44'),
(2, 10, 2, 5, 'A very wonderful institution. I recommend anyone who wants to pursue higher education to choose this University.', '2025-04-15 11:58:12'),
(3, 3, 18, 4, 'John\'s Plumbing company gets the work done in a very short time. \r\nHe is the best in the region guys', '2025-04-15 12:10:16'),
(4, 68, 18, 4, 'Great stuff, come and give them a visit', '2025-04-15 12:12:19'),
(5, 49, 2, 4, 'great place', '2025-04-15 12:19:19'),
(6, 140, 18, 1, 'scam', '2025-04-15 12:31:53'),
(7, 156, 19, 4, 'they gave me an original jersey and boots.\r\ni recommend every one to check them out.\r\nthe prices are a bit high', '2025-04-29 20:12:54'),
(8, 156, 2, 4, 'they have nice things but very  expensive my guy.', '2025-04-29 20:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(191) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','editor','moderator','business','viewer') NOT NULL DEFAULT 'viewer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `role`, `created_at`) VALUES
(2, 'admin1', '$2y$10$JD48zlcL1f4r.VN/bX7Jou/WCdJGbbeHHr.KKIvXVuKaJZFV8FrLO', 'admin', '2025-03-01 09:00:02'),
(15, 'sam', '$2y$10$p3sNcr0IK1ZCf397HWDeJ.QuGOgLBcHxadHGtkZzrIF3rXs3/eKBi', 'admin', '2025-04-07 10:03:48'),
(17, 'James', '$2y$10$aqntPPD5nEp6WTOP.GYj8OsUT7GWAfhiLNOMjua.7aWP18kl.lQI6', 'viewer', '2025-04-11 06:21:25'),
(18, 'Musa', '$2y$10$uob6uAB8GotFI321OhdSHuQjHysx1CjHPZ1c0Xlrodp7H3ZW4GmXi', 'viewer', '2025-04-15 12:08:49'),
(19, 'martin', '$2y$10$v9cUpvDm.OJntbwi17v7Mu9J54wXKLk5H120xMyn4wJq9dJJLGsfK', 'viewer', '2025-04-29 20:11:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `businesses`
--
ALTER TABLE `businesses` ADD FULLTEXT KEY `name` (`name`,`description`);
ALTER TABLE `businesses` ADD FULLTEXT KEY `name_2` (`name`,`description`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories` ADD FULLTEXT KEY `category_name` (`category_name`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews` ADD FULLTEXT KEY `review_text` (`review_text`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
