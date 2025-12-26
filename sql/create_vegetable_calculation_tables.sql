-- Vegetable calculation session tables

CREATE TABLE IF NOT EXISTS vegetable_calc_session (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATE NOT NULL,
  shift VARCHAR(10) NOT NULL,
  customer_id INT NOT NULL,
  total_menu INT DEFAULT 0,
  total_bahan INT DEFAULT 0,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vegetable_calc_session_menu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id INT NOT NULL,
  menu_harian_id INT NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_vcs_session FOREIGN KEY (session_id) REFERENCES vegetable_calc_session(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
