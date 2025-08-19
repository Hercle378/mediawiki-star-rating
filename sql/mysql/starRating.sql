CREATE TABLE star_rating (
  id INT AUTO_INCREMENT PRIMARY KEY,
  page_id INT NOT NULL,
  tag_id VARCHAR(64) NOT NULL,
  user_id VARCHAR(50),
  rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_rating (page_id, tag_id, user_id)
);