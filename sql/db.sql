-- ===============================
-- 1️⃣ White Label Clients (SaaS Layer)
-- ===============================
CREATE TABLE white_label_clients (
                                     id CHAR(36) PRIMARY KEY,
                                     company_name VARCHAR(255) NOT NULL,
                                     primary_domain VARCHAR(255),
                                     logo_url TEXT,
                                     primary_color VARCHAR(50),
                                     secondary_color VARCHAR(50),
                                     support_email VARCHAR(255),
                                     status ENUM('active','inactive') DEFAULT 'active',
                                     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE white_label_settings (
                                      id CHAR(36) PRIMARY KEY,
                                      white_label_id CHAR(36) NOT NULL,
                                      enable_loans BOOLEAN DEFAULT TRUE,
                                      enable_credits BOOLEAN DEFAULT TRUE,
                                      enable_taxes BOOLEAN DEFAULT TRUE,
                                      enable_insurance BOOLEAN DEFAULT TRUE,
                                      enable_withdrawals BOOLEAN DEFAULT TRUE,
                                      enable_rm_targets BOOLEAN DEFAULT TRUE,
                                      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                      FOREIGN KEY (white_label_id) REFERENCES white_label_clients(id)
);

CREATE TABLE white_label_domains (
                                     id CHAR(36) PRIMARY KEY,
                                     white_label_id CHAR(36) NOT NULL,
                                     domain VARCHAR(255),
                                     is_primary BOOLEAN DEFAULT FALSE,
                                     status ENUM('active','inactive') DEFAULT 'active',
                                     FOREIGN KEY (white_label_id) REFERENCES white_label_clients(id)
);

-- ===============================
-- 2️⃣ Partners / DSA (Business Tenants)
-- ===============================
CREATE TABLE partners (
                          id CHAR(36) PRIMARY KEY,
                          white_label_id CHAR(36),
                          partner_type ENUM('PLATFORM','WHITE_LABEL') NOT NULL,
                          name VARCHAR(255) NOT NULL,
                          email VARCHAR(255),
                          phone VARCHAR(20),
                          status ENUM('active','inactive') DEFAULT 'active',
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          created_by CHAR(36),
                          rm_id CHAR(36),
                          kyc_status ENUM('PENDING', 'VERIFIED', 'REJECTED') DEFAULT 'PENDING',
                          agreement_accepted_at TIMESTAMP NULL DEFAULT NULL,
                          FOREIGN KEY (white_label_id) REFERENCES white_label_clients(id),
                          FOREIGN KEY (created_by) REFERENCES users(id),
                          FOREIGN KEY (rm_id) REFERENCES users(id)
);

CREATE TABLE partner_profiles (
                                  id CHAR(36) PRIMARY KEY,
                                  partner_id CHAR(36) NOT NULL,
                                  profile_image TEXT,
                                  full_name VARCHAR(255),
                                  mobile VARCHAR(20),
                                  email VARCHAR(255),
                                  whatsapp VARCHAR(20),
                                  dob DATE,
                                  gender ENUM('male','female'),
                                  FOREIGN KEY (partner_id) REFERENCES partners(id)
);

CREATE TABLE partner_addresses (
                                   id CHAR(36) PRIMARY KEY,
                                   partner_id CHAR(36) NOT NULL,
                                   type ENUM('permanent','office'),
                                   address TEXT,
                                   state VARCHAR(100),
                                   city VARCHAR(100),
                                   pincode VARCHAR(10),
                                   FOREIGN KEY (partner_id) REFERENCES partners(id)
);

CREATE TABLE partner_identity (
                                  id CHAR(36) PRIMARY KEY,
                                  partner_id CHAR(36) NOT NULL,
                                  gst VARCHAR(20),
                                  aadhaar VARCHAR(20),
                                  pan VARCHAR(20),
                                  FOREIGN KEY (partner_id) REFERENCES partners(id)
);

CREATE TABLE partner_documents (
        id CHAR(36) PRIMARY KEY,
        partner_id CHAR(36) NOT NULL,
        document_type VARCHAR(100) NOT NULL, -- e.g., 'AADHAAR_FRONT', 'PAN_CARD'
        file_url TEXT NOT NULL,
        status ENUM('UPLOADED', 'VERIFIED', 'REJECTED') DEFAULT 'UPLOADED',
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        verified_by CHAR(36), -- User ID of RM/Admin
        verified_at TIMESTAMP NULL,
        FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE,
        FOREIGN KEY (verified_by) REFERENCES users(id)
);

-- ===============================
-- 3️⃣ Roles & Users
-- ===============================
CREATE TABLE roles (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       code ENUM('SUPER_ADMIN','RM','SALES_EXEC','PARTNER_ADMIN') UNIQUE,
                       name VARCHAR(50)
);

CREATE TABLE users (
                       id CHAR(36) PRIMARY KEY,
                       white_label_id CHAR(36),
                       partner_id CHAR(36),
                       role_id INT NOT NULL,
                       first_name VARCHAR(100),
                       last_name VARCHAR(100),
                       email VARCHAR(255) UNIQUE,
                       phone VARCHAR(20),
                       password_hash TEXT,
                       profile_image TEXT NULL,
                       status ENUM('active','inactive') DEFAULT 'active',
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       FOREIGN KEY (white_label_id) REFERENCES white_label_clients(id),
                       FOREIGN KEY (partner_id) REFERENCES partners(id),
                       FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE user_bank_details (
                                   id CHAR(36) PRIMARY KEY,
                                   user_id CHAR(36) NOT NULL,
                                   account_holder_name VARCHAR(255),
                                   bank_name VARCHAR(255),
                                   account_number VARCHAR(50),
                                   ifsc_code VARCHAR(20),
                                   branch VARCHAR(255),
                                   FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ===============================
-- 4️⃣ Services & Subscriptions
-- ===============================
CREATE TABLE services (
                          id CHAR(36) PRIMARY KEY,
                          code VARCHAR(50) UNIQUE,
                          name VARCHAR(255),
                          category ENUM('LOAN','CREDIT','TAX','INSURANCE','OTHER'),
                          base_price DECIMAL(15,2),
                          igst_rate DECIMAL(5,2),
                          challan_required BOOLEAN DEFAULT FALSE,
                          is_active BOOLEAN DEFAULT TRUE,
                          image_url TEXT,
                          description TEXT,
                          url TEXT,
                          service_type ENUM('EXTERNAL_REDIRECT', 'INTERNAL_FORM') NOT NULL DEFAULT 'INTERNAL_FORM',
                          parent_id CHAR(36) NULL DEFAULT NULL,
                          form_type ENUM('NONE', 'GOVT_LOAN', 'PRIVATE_LOAN') NOT NULL DEFAULT 'NONE',
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          FOREIGN KEY (parent_id) REFERENCES services(id) ON DELETE SET NULL
);

CREATE TABLE white_label_services (
                                      id CHAR(36) PRIMARY KEY,
                                      white_label_id CHAR(36) NOT NULL,
                                      service_id CHAR(36) NOT NULL,
                                      custom_price DECIMAL(15,2),
                                      incentive_rate DECIMAL(5,2),
                                      is_enabled BOOLEAN DEFAULT TRUE,
                                      FOREIGN KEY (white_label_id) REFERENCES white_label_clients(id),
                                      FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE partner_services (
                                  id CHAR(36) PRIMARY KEY,
                                  partner_id CHAR(36) NOT NULL,
                                  service_id CHAR(36) NOT NULL,
                                  is_active BOOLEAN DEFAULT TRUE,
                                  FOREIGN KEY (partner_id) REFERENCES partners(id),
                                  FOREIGN KEY (service_id) REFERENCES services(id)
);

-- ===============================
-- 5️⃣ Service Applications & Dynamic Data
-- ===============================
CREATE TABLE service_applications (
                                      id CHAR(36) PRIMARY KEY,
                                      white_label_id CHAR(36),
                                      partner_id CHAR(36),
                                      service_id CHAR(36) NOT NULL,
                                      created_by CHAR(36),
                                      customer_name VARCHAR(255),
                                      customer_email VARCHAR(255),
                                      customer_phone VARCHAR(20),
                                      status ENUM('submitted','under_verification','approved','rejected','completed') DEFAULT 'submitted',
                                      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                      FOREIGN KEY (white_label_id) REFERENCES white_label_clients(id),
                                      FOREIGN KEY (partner_id) REFERENCES partners(id),
                                      FOREIGN KEY (service_id) REFERENCES services(id),
                                      FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE service_application_meta (
                                          id CHAR(36) PRIMARY KEY,
                                          application_id CHAR(36) NOT NULL,
                                          field_key VARCHAR(255),
                                          field_value TEXT,
                                          FOREIGN KEY (application_id) REFERENCES service_applications(id)
);

-- ===============================
-- 6️⃣ Documents & Comments
-- ===============================
CREATE TABLE documents (
                           id CHAR(36) PRIMARY KEY,
                           application_id CHAR(36) NOT NULL,
                           document_type VARCHAR(100),
                           file_url TEXT,
                           uploaded_by CHAR(36),
                           uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           FOREIGN KEY (application_id) REFERENCES service_applications(id),
                           FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

CREATE TABLE comments (
                          id CHAR(36) PRIMARY KEY,
                          application_id CHAR(36) NOT NULL,
                          user_id CHAR(36) NOT NULL,
                          comment TEXT,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          FOREIGN KEY (application_id) REFERENCES service_applications(id),
                          FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE verification_logs (
                                   id CHAR(36) PRIMARY KEY,
                                   application_id CHAR(36) NOT NULL,
                                   rm_id CHAR(36) NOT NULL,
                                   action ENUM('approved','rejected','query_raised'),
                                   remarks TEXT,
                                   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                   FOREIGN KEY (application_id) REFERENCES service_applications(id),
                                   FOREIGN KEY (rm_id) REFERENCES users(id)
);

-- ===============================
-- 7️⃣ RM Targets & Withdrawals
-- ===============================
CREATE TABLE rm_targets (
                            id CHAR(36) PRIMARY KEY,
                            rm_id CHAR(36) NOT NULL,
                            service_id CHAR(36) NOT NULL,
                            target_amount DECIMAL(15,2),
                            achieved_amount DECIMAL(15,2),
                            status ENUM('open','completed') DEFAULT 'open',
                            month VARCHAR(7),
                            FOREIGN KEY (rm_id) REFERENCES users(id),
                            FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE withdrawals (
                             id CHAR(36) PRIMARY KEY,
                             user_id CHAR(36) NOT NULL,
                             gross_amount DECIMAL(15,2),
                             tds_amount DECIMAL(15,2),
                             net_amount DECIMAL(15,2),
                             status ENUM('requested','approved','paid') DEFAULT 'requested',
                             account_holder_name VARCHAR(255),
                             bank_name VARCHAR(255),
                             bank_account_number VARCHAR(50),
                             ifsc_code VARCHAR(20),
                             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                             FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ===============================
-- 8️⃣ Banks & Pincodes
-- ===============================
CREATE TABLE banks (
                       id CHAR(36) PRIMARY KEY,
                       name VARCHAR(255)
);

CREATE TABLE bank_pincodes (
                               id CHAR(36) PRIMARY KEY,
                               bank_id CHAR(36) NOT NULL,
                               pincode VARCHAR(10),
                               FOREIGN KEY (bank_id) REFERENCES banks(id)
);

-- ===============================
-- 9️⃣ Security
-- ===============================
CREATE TABLE blacklisted_tokens (
                                    id CHAR(36) PRIMARY KEY,
                                    token TEXT,
                                    expires_at TIMESTAMP,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE partner_subscriptions (
                                       id CHAR(36) PRIMARY KEY,
                                       partner_id CHAR(36) NOT NULL,
                                       plan_name VARCHAR(100), -- e.g., Silver, Gold
                                       payment_amount DECIMAL(15,2),
                                       due_amount DECIMAL(15,2),
                                       payment_mode ENUM('Online', 'Cash', 'Cheque'),
                                       transaction_id VARCHAR(255),
                                       status ENUM('active', 'expired', 'pending') DEFAULT 'active',
                                       start_date DATE,
                                       end_date DATE,
                                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                       FOREIGN KEY (partner_id) REFERENCES partners(id)
);

-- ===============================
-- 10. Subscription Plans
-- ===============================
CREATE TABLE subscription_plans (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    gst_rate DECIMAL(5,2) DEFAULT 18.00,
    description TEXT,
    footer_description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE subscription_plan_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id CHAR(36) NOT NULL,
    service_id CHAR(36) NOT NULL,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);
