/************
   TABLE CREATION
************/
CREATE TABLE User_ (
    id INTEGER PRIMARY KEY,
    name_ TEXT NOT NULL,
    password_ TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE CHECK( email LIKE '%@%.%' AND LENGTH(email) >= 6),
    username TEXT NOT NULL UNIQUE,
    web_link TEXT,
    phone_number TEXT CHECK (LENGTH(phone_number) >= 9),
    profile_photo INTEGER,
    is_admin BOOLEAN NOT NULL DEFAULT false,
    creation_date DATE NOT NULL DEFAULT (DATE('now')),
    is_freelancer BOOLEAN NOT NULL DEFAULT false,
    currency TEXT NOT NULL DEFAULT "eur" CHECK (currency IN ('eur', 'usd', 'gbp', 'brl')),
    is_blocked BOOLEAN NOT NULL DEFAULT false,
    night_mode BOOLEAN NOT NULL DEFAULT false,
    FOREIGN KEY (profile_photo) REFERENCES Media(id)
);

CREATE TABLE Prime (
    id INTEGER PRIMARY KEY,
    user_id INTEGER UNIQUE NOT NULL,
    exp_date DATE,
    start_date DATE NOT NULL DEFAULT (DATE('now')),
    FOREIGN KEY (user_id) REFERENCES User_(id)
);

CREATE TABLE Payment (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    card_num TEXT NOT NULL CHECK ( LENGTH(card_num) = 16 AND card_num GLOB '[0-9]*' ),
    exp_month INTEGER NOT NULL CHECK (exp_month > 0),
    exp_year INTEGER NOT NULL CHECK (exp_year > 0),
    code_ TEXT NOT NULL CHECK ( LENGTH(code_) = 3 AND code_ GLOB '[0-9]*' ),
    FOREIGN KEY (user_id) REFERENCES User_(id)
);

CREATE TABLE Address_ (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    street TEXT NOT NULL,
    door_num TEXT NOT NULL,
    floor_ TEXT,
    extra TEXT,
    district TEXT NOT NULL,
    municipality TEXT NOT NULL,
    zip_code TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User_(id)
);

CREATE TABLE Reason_Block (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    reason TEXT NOT NULL CHECK(reason IN (
                                    "Comportamento abusivo ou inapropriado",
                                    "Atividades fraudulentas",
                                    "Violação das regras da plataforma",
                                    "Problemas recorrentes em serviços",
                                    "Violação de privacidade ou segurança",
                                    "Inatividade prolongada com indícios de abandono",
                                    "Violação dos termos de serviço",
                                    "Outra"
                                    )),
    extra_info TEXT,
    FOREIGN KEY (user_id) REFERENCES User_(id)
);

CREATE TABLE Unblock_Appeal (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    body_ TEXT NOT NULL,
    date_ DATE NOT NULL DEFAULT (DATE('now')),
    time_ TIME NOT NULL DEFAULT (TIME('now')),
    status_ TEXT NOT NULL CHECK(status_ IN ('approved', 'rejected', 'pending')),
    FOREIGN KEY (user_id) REFERENCES User_(id)
);

CREATE TABLE Media (
    id INTEGER PRIMARY KEY,
    service_id INTEGER,
    path_ TEXT NOT NULL,
    title TEXT,
    FOREIGN KEY (service_id) REFERENCES Service_(id)
);

CREATE TABLE Service_ (
    id INTEGER PRIMARY KEY,
    freelancer_id INTEGER NOT NULL,
    name_ TEXT NOT NULL,
    description_ TEXT NOT NULL,
    duration INTEGER NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    price_per_hour REAL NOT NULL,
    promotion INT NOT NULL DEFAULT 0,  /* O valor que estiver será o valor do desconto; default 0, logo por padrão não há desconto*/
    category_id INTEGER NOT NULL,
    FOREIGN KEY (freelancer_id) REFERENCES User_(id),
    FOREIGN KEY (category_id) REFERENCES Category(id)
);

CREATE TABLE Category (
    id INTEGER PRIMARY KEY,
    name_ TEXT NOT NULL UNIQUE,
    photo_id INTEGER NOT NULL,
    FOREIGN KEY(photo_id) REFERENCES Media(id)
);

CREATE TABLE Service_Data (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    date_ DATE NOT NULL DEFAULT (DATE('now')),
    time_ TIME NOT NULL DEFAULT (TIME('now')),
    travel_fee INTEGER NOT NULL DEFAULT 0,
    final_price REAL NOT NULL,
    status_ TEXT NOT NULL CHECK(status_ IN ('completed', 'accepted', 'paid')),
    FOREIGN KEY (user_id) REFERENCES User_(id),
    FOREIGN KEY (service_id) REFERENCES Service_(id)
);

CREATE TABLE Feedback (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    title TEXT,
    description_ TEXT,
    evaluation REAL NOT NULL CHECK(evaluation >= 0 AND evaluation <= 5 AND (evaluation * 2) = CAST(evaluation * 2 AS INTEGER)), /* 0; 0.5; 1; 1.5; 2; ... 5*/
    date_ DATE NOT NULL DEFAULT (DATE('now')),
    time_ TIME NOT NULL DEFAULT (TIME('now')),
    FOREIGN KEY (user_id) REFERENCES User_(id),
    FOREIGN KEY (service_id) REFERENCES Service_(id)
);

CREATE TABLE Message_ (
    id INTEGER PRIMARY KEY,
    sender_id INTEGER NOT NULL,
    receiver_id INTEGER NOT NULL,
    body_ TEXT NOT NULL,
    date_ DATE NOT NULL,
    time_ TIME NOT NULL,
    is_read BOOLEAN NOT NULL DEFAULT false,
    FOREIGN KEY (sender_id) REFERENCES User_(id),
    FOREIGN KEY (receiver_id) REFERENCES User_(id)
);

CREATE TABLE Request (
    id INTEGER PRIMARY KEY,
    service_data_id INTEGER NOT NULL,
    message_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    price REAL NOT NULL,
    duration INTEGER NOT NULL,
    FOREIGN KEY (service_data_id) REFERENCES Service_Data(id),
    FOREIGN KEY (message_id) REFERENCES Message_(id)
);

CREATE TABLE Complaint (    /* Antiga classe Disputa*/
    id INTEGER PRIMARY KEY,
    service_data_id INTEGER NOT NULL,
    message_id INTEGER NOT NULL,
    admin_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    body_ TEXT NOT NULL,
    date_ DATE NOT NULL DEFAULT (DATE('now')),
    time_ TIME NOT NULL DEFAULT (TIME('now')),
    is_accepted BOOLEAN NOT NULL DEFAULT false,
    FOREIGN KEY (service_data_id) REFERENCES Service_Data(id),
    FOREIGN KEY (message_id) REFERENCES Message_(id),
    FOREIGN KEY (admin_id) REFERENCES User_(id)
);

CREATE TABLE Newsletter_email(
    id INTEGER PRIMARY KEY,
    email TEXT NOT NULL
);

CREATE TABLE distances_Porto (
  origin TEXT NOT NULL,
  destiny TEXT NOT NULL,
  distance INTEGER NOT NULL CHECK (distance >= 0),
  PRIMARY KEY (origin, destiny)
);

CREATE TABLE distances_Districts (
  origin TEXT,
  destiny TEXT,
  distance INTEGER NOT NULL CHECK (distance >= 0),
  PRIMARY KEY (origin, destiny)
);

CREATE TABLE Contact (
    id INTEGER PRIMARY KEY,
    name_ TEXT NOT NULL,
    email TEXT NOT NULL CHECK( email LIKE '%@%.%' AND LENGTH(email) >= 6),
    phone TEXT,
    subject TEXT NOT NULL,
    message_ TEXT NOT NULL,
    created_at DATE NOT NULL DEFAULT (DATE('now')),
    created_time TIME NOT NULL DEFAULT (TIME('now')),
    is_read BOOLEAN NOT NULL DEFAULT false,
    admin_response TEXT,
    response_date DATE,
    response_time TIME
);

/************
   TRIGGERS
************/
# Trigger para colocar a Data de Expiração de uma subscrição Prime (1 mês depois);
CREATE TRIGGER setExpDate_TablePrime
AFTER INSERT ON Prime
FOR EACH ROW
WHEN NEW.exp_date IS NULL
BEGIN
    UPDATE Prime
    SET exp_date = DATE(NEW.start_date, '+1 month')
    WHERE id = NEW.id;
END;

# Trigger para calcular e inserir o preço final de um serviço contratado (duration * price_per_hour * (1 - promotion / 100.0));
CREATE TRIGGER setFinalPrice_TableService_Data
AFTER INSERT ON Service_Data
FOR EACH ROW
BEGIN
    UPDATE Service_Data
    SET final_price = (
        SELECT duration * price_per_hour * (1 - promotion / 100.0)
        FROM Service_
        WHERE id = NEW.service_id
    )
    WHERE id = NEW.id;
END;

/************
   INDEXES
************/

CREATE INDEX index_user_email ON User_(email);
CREATE INDEX index_user_username ON User_(username);
CREATE INDEX index_service_freelancer ON Service_(freelancer_id);
CREATE INDEX idx_service_data_date ON Service_Data(date_, time_);

/*
Coisas importantes a fazer em código:
    -> Renovação de prime;
    -> Cálculo custo de deslocação (??) -> Fazer isto a partir da db;
*/

/* Ver criação de Indexes nas tabelas User_, Service_ (mais alguma necessária?)     -> Todas as fk, para facilitar joins*/
/* Fazer o populate.sql*/