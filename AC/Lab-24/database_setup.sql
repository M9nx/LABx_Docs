-- Lab 24: IDOR Exposes All Machine Learning Models
-- Database Setup for MLRegistry Platform

DROP DATABASE IF EXISTS ac_lab24;
CREATE DATABASE ac_lab24;
USE ac_lab24;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100),
    avatar_url VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Projects table (like GitLab projects)
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    owner_id INT NOT NULL,
    visibility ENUM('public', 'private', 'internal') DEFAULT 'private',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id)
);

-- ML Models table (the vulnerable resource)
CREATE TABLE ml_models (
    id INT PRIMARY KEY AUTO_INCREMENT,
    internal_id INT UNIQUE NOT NULL,  -- Sequential ID used in GID (vulnerable!)
    name VARCHAR(100) NOT NULL,
    description TEXT,
    project_id INT NOT NULL,
    owner_id INT NOT NULL,
    visibility ENUM('public', 'private') DEFAULT 'private',
    version_count INT DEFAULT 0,
    candidate_count INT DEFAULT 0,
    framework VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (owner_id) REFERENCES users(id)
);

-- Model Versions table
CREATE TABLE model_versions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    internal_id INT UNIQUE NOT NULL,  -- Sequential ID (also vulnerable)
    model_id INT NOT NULL,
    version VARCHAR(20) NOT NULL,
    description TEXT,
    package_id VARCHAR(50),
    accuracy DECIMAL(5,4),
    loss DECIMAL(10,6),
    training_data_size INT,
    hyperparameters JSON,
    artifact_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (model_id) REFERENCES ml_models(id)
);

-- Model Candidates (experiment tracking)
CREATE TABLE model_candidates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    model_id INT NOT NULL,
    version_id INT,
    name VARCHAR(100),
    iid INT,
    eid VARCHAR(50),
    status ENUM('running', 'completed', 'failed') DEFAULT 'running',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (model_id) REFERENCES ml_models(id),
    FOREIGN KEY (version_id) REFERENCES model_versions(id)
);

-- Model Parameters
CREATE TABLE model_params (
    id INT PRIMARY KEY AUTO_INCREMENT,
    candidate_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    value TEXT,
    FOREIGN KEY (candidate_id) REFERENCES model_candidates(id)
);

-- Model Metadata
CREATE TABLE model_metadata (
    id INT PRIMARY KEY AUTO_INCREMENT,
    candidate_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    value TEXT,
    FOREIGN KEY (candidate_id) REFERENCES model_candidates(id)
);

-- Model Metrics
CREATE TABLE model_metrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    candidate_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    value DECIMAL(20,10),
    step INT DEFAULT 0,
    FOREIGN KEY (candidate_id) REFERENCES model_candidates(id)
);

-- Activity Log
CREATE TABLE activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100),
    resource_type VARCHAR(50),
    resource_id INT,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users
INSERT INTO users (username, password, email, full_name, avatar_url, role) VALUES
('attacker', 'attacker123', 'attacker@evil.com', 'Attack User', '/avatars/attacker.png', 'user'),
('victim_corp', 'victim123', 'ml-team@victimcorp.com', 'VictimCorp ML Team', '/avatars/victim.png', 'user'),
('data_scientist', 'scientist123', 'scientist@research.org', 'Dr. Sarah Chen', '/avatars/sarah.png', 'user'),
('admin', 'admin123', 'admin@mlregistry.local', 'System Administrator', '/avatars/admin.png', 'admin');

-- Insert projects
INSERT INTO projects (name, description, owner_id, visibility) VALUES
('attacker-public-project', 'Public ML experiments', 1, 'public'),
('victimcorp-fraud-detection', 'CONFIDENTIAL: Fraud detection models for banking', 2, 'private'),
('victimcorp-customer-churn', 'PRIVATE: Customer churn prediction', 2, 'private'),
('research-nlp-models', 'RESTRICTED: NLP research models', 3, 'private'),
('research-medical-imaging', 'CONFIDENTIAL: Medical diagnosis AI', 3, 'private');

-- Insert ML Models (with sequential internal_ids - VULNERABLE!)
-- Attacker's models (public)
INSERT INTO ml_models (internal_id, name, description, project_id, owner_id, visibility, version_count, candidate_count, framework) VALUES
(1000500, 'basic-classifier', 'Simple image classifier', 1, 1, 'public', 2, 3, 'TensorFlow');

-- VictimCorp's PRIVATE models (TARGET - internal_ids 1000501-1000504)
INSERT INTO ml_models (internal_id, name, description, project_id, owner_id, visibility, version_count, candidate_count, framework) VALUES
(1000501, 'fraud-detection-v3', 'CONFIDENTIAL: Real-time fraud detection using transaction patterns. API_KEY: sk_live_fraud_4x7k9m2p5q8w1z3y', 2, 2, 'private', 3, 5, 'PyTorch'),
(1000502, 'anomaly-detector', 'PRIVATE: Detects unusual banking patterns. Threshold: 0.87, Secret_Salt: fr4ud_d3t3ct_s4lt', 2, 2, 'private', 2, 4, 'TensorFlow'),
(1000503, 'customer-churn-predictor', 'CONFIDENTIAL: Predicts customer churn. Revenue impact: $4.2M/year', 3, 2, 'private', 4, 8, 'scikit-learn'),
(1000504, 'credit-risk-model', 'RESTRICTED: Credit scoring model. Bias_audit_token: cr3d1t_4ud1t_t0k3n', 3, 2, 'private', 2, 3, 'XGBoost');

-- Research Lab's PRIVATE models (TARGET - internal_ids 1000505-1000507)
INSERT INTO ml_models (internal_id, name, description, project_id, owner_id, visibility, version_count, candidate_count, framework) VALUES
(1000505, 'sentiment-analyzer-bert', 'PRIVATE: Fine-tuned BERT for sentiment. HuggingFace_Token: hf_priv_t0k3n_x7y8z9', 4, 3, 'private', 3, 6, 'Transformers'),
(1000506, 'medical-xray-classifier', 'CONFIDENTIAL: X-ray diagnosis model. FDA_Submission_ID: FDA-2024-ML-00892', 5, 3, 'private', 5, 12, 'PyTorch'),
(1000507, 'cancer-detection-cnn', 'RESTRICTED: Cancer detection from mammograms. Accuracy: 97.3%, Patent_Pending: PCT/US2024/012345', 5, 3, 'private', 4, 9, 'TensorFlow');

-- Insert Model Versions (also with sequential internal_ids)
-- Attacker's versions
INSERT INTO model_versions (internal_id, model_id, version, description, package_id, accuracy, loss, training_data_size, hyperparameters, artifact_path) VALUES
(2000100, 1, '1.0.0', 'Initial release', 'pkg-001', 0.8500, 0.234500, 10000, '{"learning_rate": 0.001, "epochs": 50}', '/artifacts/basic-classifier/v1.0.0');

-- VictimCorp's PRIVATE versions (TARGET)
INSERT INTO model_versions (internal_id, model_id, version, description, package_id, accuracy, loss, training_data_size, hyperparameters, artifact_path) VALUES
(2000101, 2, '3.2.1', 'Production fraud model - DO NOT SHARE. S3_Bucket: s3://victimcorp-ml-prod/fraud/', 'pkg-fraud-321', 0.9650, 0.045200, 5000000, '{"learning_rate": 0.0001, "batch_size": 256, "secret_key": "prod_ml_k3y_x9z8"}', '/artifacts/fraud-detection/v3.2.1'),
(2000102, 2, '3.1.0', 'Staging fraud model', 'pkg-fraud-310', 0.9580, 0.052100, 4500000, '{"learning_rate": 0.0001, "batch_size": 128}', '/artifacts/fraud-detection/v3.1.0'),
(2000103, 3, '2.0.0', 'Anomaly detection prod - DB_CONN: mysql://prod:p4ssw0rd@db.victimcorp.internal', 'pkg-anomaly-200', 0.9120, 0.089700, 2000000, '{"threshold": 0.87, "window_size": 100}', '/artifacts/anomaly/v2.0.0'),
(2000104, 4, '4.1.0', 'Churn model - Salesforce_API: sf_api_k3y_ch8rn_pr3d', 'pkg-churn-410', 0.8890, 0.112300, 1500000, '{"features": 47, "ensemble": true}', '/artifacts/churn/v4.1.0'),
(2000105, 5, '2.5.0', 'Credit risk - Equifax_Integration_Key: eq_1nt3gr4t10n_k3y', 'pkg-credit-250', 0.9230, 0.078500, 3000000, '{"risk_bands": 5, "regulatory_compliant": true}', '/artifacts/credit/v2.5.0');

-- Research Lab's PRIVATE versions
INSERT INTO model_versions (internal_id, model_id, version, description, package_id, accuracy, loss, training_data_size, hyperparameters, artifact_path) VALUES
(2000106, 6, '3.0.0', 'BERT sentiment - AWS_SageMaker_Role: arn:aws:iam::123456789:role/MLRole', 'pkg-bert-300', 0.9340, 0.067800, 800000, '{"model": "bert-base", "fine_tune_epochs": 10}', '/artifacts/sentiment/v3.0.0'),
(2000107, 7, '5.2.0', 'X-ray classifier - HIPAA_Compliant, Patient_Data_Key: h1p44_k3y_m3d1c4l', 'pkg-xray-520', 0.9720, 0.031200, 250000, '{"architecture": "ResNet50", "pretrained": true}', '/artifacts/xray/v5.2.0'),
(2000108, 8, '4.0.0', 'Cancer detection - Research_Grant: NIH-R01-CA-234567, Unpublished_Results', 'pkg-cancer-400', 0.9730, 0.028900, 150000, '{"architecture": "EfficientNet", "augmentation": true}', '/artifacts/cancer/v4.0.0');

-- Insert candidates
INSERT INTO model_candidates (model_id, version_id, name, iid, eid, status) VALUES
(1, 1, 'experiment-1', 1, 'exp-basic-001', 'completed'),
(2, 2, 'fraud-experiment-prod', 1, 'exp-fraud-prod-001', 'completed'),
(2, 3, 'fraud-experiment-staging', 2, 'exp-fraud-stg-001', 'completed'),
(3, 4, 'anomaly-main', 1, 'exp-anomaly-001', 'completed'),
(4, 5, 'churn-final', 1, 'exp-churn-001', 'completed'),
(5, 6, 'credit-approved', 1, 'exp-credit-001', 'completed'),
(6, 7, 'bert-finetune', 1, 'exp-bert-001', 'completed'),
(7, 8, 'xray-production', 1, 'exp-xray-001', 'completed'),
(8, 9, 'cancer-research', 1, 'exp-cancer-001', 'running');

-- Insert model parameters (sensitive data)
INSERT INTO model_params (candidate_id, name, value) VALUES
(2, 'api_endpoint', 'https://api.victimcorp.com/v1/fraud'),
(2, 'model_secret', 'msk_pr0d_fr4ud_s3cr3t'),
(3, 'staging_endpoint', 'https://staging-api.victimcorp.com/fraud'),
(4, 'db_password', 'an0m4ly_db_p4ss'),
(5, 'salesforce_secret', 'sf_0auth_s3cr3t_k3y'),
(6, 'credit_bureau_key', 'cr3d1t_bur34u_4p1_k3y'),
(7, 'huggingface_token', 'hf_pr1v4t3_t0k3n_b3rt'),
(8, 'hipaa_encryption_key', 'h1p44_3ncrypt10n_k3y_2024'),
(9, 'research_db_conn', 'postgres://research:r3s34rch_p4ss@db.research.org/cancer');

-- Insert model metrics
INSERT INTO model_metrics (candidate_id, name, value, step) VALUES
(2, 'accuracy', 0.9650, 100),
(2, 'precision', 0.9580, 100),
(2, 'recall', 0.9720, 100),
(2, 'f1_score', 0.9649, 100),
(3, 'accuracy', 0.9580, 80),
(4, 'auc_roc', 0.9450, 50),
(5, 'churn_rate_reduction', 0.2340, 100),
(6, 'default_rate', 0.0320, 100),
(7, 'sentiment_accuracy', 0.9340, 100),
(8, 'diagnosis_accuracy', 0.9720, 100),
(9, 'sensitivity', 0.9680, 100),
(9, 'specificity', 0.9780, 100);

-- Insert model metadata (more sensitive info)
INSERT INTO model_metadata (candidate_id, name, value) VALUES
(2, 'deployment_env', 'production'),
(2, 'cost_per_inference', '$0.0012'),
(2, 'monthly_requests', '45,000,000'),
(3, 'internal_notes', 'Staging before Q4 release'),
(4, 'compliance_status', 'PCI-DSS Certified'),
(5, 'business_impact', '$4.2M annual revenue protection'),
(6, 'regulatory_approval', 'Fed Reserve Approved'),
(7, 'training_cost', '$125,000 AWS compute'),
(8, 'fda_status', 'Pending 510(k) clearance'),
(9, 'publication_status', 'Embargoed until Nature submission');

CREATE INDEX idx_models_internal_id ON ml_models(internal_id);
CREATE INDEX idx_versions_internal_id ON model_versions(internal_id);
CREATE INDEX idx_models_owner ON ml_models(owner_id);
CREATE INDEX idx_models_project ON ml_models(project_id);
