BEGIN TRANSACTION;

-- Desativar temporariamente as restrições de chave estrangeira
PRAGMA foreign_keys = OFF;

-- Limpar todas as tabelas
DELETE FROM Complaint;
DELETE FROM Request;
DELETE FROM Message_;
DELETE FROM Feedback;
DELETE FROM Service_Data;
DELETE FROM Address_;
DELETE FROM Payment;
DELETE FROM Prime;
DELETE FROM Media;
DELETE FROM Reason_Block;
DELETE FROM Unblock_Appeal;
DELETE FROM Service_;
DELETE FROM Category;
DELETE FROM User_;

-- Reativar as restrições de chave estrangeira
PRAGMA foreign_keys = ON;

/************
   MEDIA populate
************/
INSERT INTO Media (id, path_, title) VALUES
(1, '/Images/site/categories/limpeza.jpg', 'Limpeza Doméstica'),
(2, '/Images/site/categories/reparacoes.jpg', 'Reparações Gerais'),
(3, '/Images/site/categories/jardim.jpg', 'Jardim e Exterior'),
(4, '/Images/site/categories/aulas.jpg', 'Aulas Particulares'),
(5, '/Images/site/categories/design.jpg', 'Design e Tecnologia'),
(6, '/Images/site/categories/beleza.jpg', 'Serviços de Beleza'),
(7, '/Images/site/categories/transporte.jpg', 'Transporte e Mudanças'),
(8, '/Images/site/categories/culinaria.jpg', 'Culinária'),
(9, '/Images/site/categories/animais.jpg', 'Cuidados com Animais'),
(10, '/Images/site/categories/administrativo.jpg', 'Serviços Administrativos');

/************
   CATEGORIES populate
************/
INSERT INTO Category (name_, photo_id) VALUES
('Limpeza Doméstica', 1),
('Reparações Gerais', 2),
('Jardim e Exterior', 3),
('Aulas Particulares', 4),
('Design e Tecnologia', 5),
('Serviços de Beleza', 6),
('Transporte e Mudanças', 7),
('Culinária', 8),
('Cuidados com Animais', 9),
('Serviços Administrativos', 10);


/************
   USERS populate -> 2 admins, 9 freelancers, 12 regular users
************/
/************
   ADMINS
************/
INSERT INTO User_ (name_, password_, email, username, is_admin, phone_number, web_link, creation_date, currency) VALUES
('Admin Santos', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'admin.santos@homegenie.pt', 'adminsantos', TRUE, '912345678', 'linkedin.com/adminsantos', '2023-08-10', 'eur'),
('Admin Pereira', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'admin.pereira@homegenie.pt', 'adminpereira', TRUE, '923456789', 'linkedin.com/adminpereira', '2023-08-15', 'eur');

/************
   FREELANCERS
************/
INSERT INTO User_ (name_, password_, email, username, is_freelancer, phone_number, web_link, creation_date, currency) VALUES
('Joana Silva', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'joana.silva@gmail.com', 'joanasilva', TRUE, '932156789', 'joanasilva.pt', '2023-09-01', 'eur'),
('Manuel Costa', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'manuel.costa@gmail.com', 'manuelcosta', TRUE, '961234567', 'manuelcosta.pt', '2023-09-10', 'eur'),
('Sofia Martins', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'sofia.martins@gmail.com', 'sofiamartins', TRUE, '936543219', 'sofiamartins.pt', '2023-09-15', 'eur'),
('António Ferreira', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'antonio.ferreira@gmail.com', 'antonioferreira', TRUE, '914738292', 'antonioferreira.pt', '2023-09-20', 'eur'),
('Ana Rodrigues', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'ana.rodrigues@gmail.com', 'anarodrigues', TRUE, '926547891', 'anarodrigues.pt', '2023-09-25', 'eur'),
('João Oliveira', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'joao.oliveira@gmail.com', 'joaooliveira', TRUE, '918273645', 'joaooliveira.pt', '2023-10-01', 'eur'),
('Mariana Santos', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'mariana.santos@gmail.com', 'marianasantos', TRUE, '939182736', 'marianasantos.pt', '2023-10-05', 'eur'),
('Ricardo Sousa', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'ricardo.sousa@gmail.com', 'ricardosousa', TRUE, '968574839', 'ricardosousa.pt', '2023-10-10', 'eur'),
('Teresa Gomes', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'teresa.gomes@gmail.com', 'teresagomes', TRUE, '927364518', 'teresagomes.pt', '2023-10-15', 'eur');

/************
   REGULAR USERS
************/
INSERT INTO User_ (name_, password_, email, username, phone_number, creation_date, currency) VALUES
('Carlos Almeida', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'carlos.almeida@gmail.com', 'carlosalmeida', '913647281', '2023-10-20', 'eur'),
('Isabel Pinto', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'isabel.pinto@gmail.com', 'isabelpinto', '925836914', '2023-10-25', 'eur'),
('Paulo Neves', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'paulo.neves@gmail.com', 'pauloneves', '968394712', '2023-11-01', 'eur'),
('Filipa Castro', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'filipa.castro@gmail.com', 'filipacastro', '937162534', '2023-11-05', 'eur'),
('Diogo Rocha', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'diogo.rocha@gmail.com', 'diogorocha', '917263548', '2023-11-10', 'eur'),
('Catarina Mota', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'catarina.mota@gmail.com', 'catarinamota', '928374651', '2023-11-15', 'eur'),
('Tiago Lopes', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'tiago.lopes@gmail.com', 'tiagolopes', '964738291', '2023-11-20', 'eur'),
('Marta Ribeiro', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'marta.ribeiro@gmail.com', 'martaribeiro', '935647182', '2023-11-25', 'eur'),
('Bruno Cunha', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'bruno.cunha@gmail.com', 'brunocunha', '916748392', '2023-12-01', 'eur'),
('Andreia Moreira', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'andreia.moreira@gmail.com', 'andreiamoreira', '924837165', '2023-12-05', 'eur'),
('Pedro Vieira', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'pedro.vieira@gmail.com', 'pedrovieira', '963718294', '2023-12-10', 'eur'),
('Raquel Dias', '5f5ebe2b944917b8c4923228b3cc9c4ab37f0a18b747f118c88baf07c5bd62aa', 'raquel.dias@gmail.com', 'raqueldias', '933647182', '2023-12-15', 'eur');

/************
   MEDIA populate - Profile Photos
************/
INSERT INTO Media (service_id, path_, title) VALUES
-- Profile photos (ids 1-23)
(NULL, '/Images/profiles/admin_santos.jpg', 'Foto de perfil Admin Santos'),
(NULL, '/images/profiles/admin_pereira.jpg', 'Foto de perfil Admin Pereira'),
(NULL, '/images/profiles/joana_silva.jpg', 'Foto de perfil Joana Silva'),
(NULL, '/images/profiles/manuel_costa.jpg', 'Foto de perfil Manuel Costa'),
(NULL, '/images/profiles/sofia_martins.jpg', 'Foto de perfil Sofia Martins'),
(NULL, '/images/profiles/antonio_ferreira.jpg', 'Foto de perfil António Ferreira'),
(NULL, '/images/profiles/ana_rodrigues.jpg', 'Foto de perfil Ana Rodrigues'),
(NULL, '/images/profiles/joao_oliveira.jpg', 'Foto de perfil João Oliveira'),
(NULL, '/images/profiles/mariana_santos.jpg', 'Foto de perfil Mariana Santos'),
(NULL, '/images/profiles/ricardo_sousa.jpg', 'Foto de perfil Ricardo Sousa'),
(NULL, '/images/profiles/teresa_gomes.jpg', 'Foto de perfil Teresa Gomes'),
(NULL, '/images/profiles/carlos_almeida.jpg', 'Foto de perfil Carlos Almeida'),
(NULL, '/images/profiles/isabel_pinto.jpg', 'Foto de perfil Isabel Pinto'),
(NULL, '/images/profiles/paulo_neves.jpg', 'Foto de perfil Paulo Neves'),
(NULL, '/images/profiles/filipa_castro.jpg', 'Foto de perfil Filipa Castro'),
(NULL, '/images/profiles/diogo_rocha.jpg', 'Foto de perfil Diogo Rocha'),
(NULL, '/images/profiles/catarina_mota.jpg', 'Foto de perfil Catarina Mota'),
(NULL, '/images/profiles/tiago_lopes.jpg', 'Foto de perfil Tiago Lopes'),
(NULL, '/images/profiles/marta_ribeiro.jpg', 'Foto de perfil Marta Ribeiro'),
(NULL, '/images/profiles/bruno_cunha.jpg', 'Foto de perfil Bruno Cunha'),
(NULL, '/images/profiles/andreia_moreira.jpg', 'Foto de perfil Andreia Moreira'),
(NULL, '/images/profiles/pedro_vieira.jpg', 'Foto de perfil Pedro Vieira'),
(NULL, '/images/profiles/raquel_dias.jpg', 'Foto de perfil Raquel Dias');

/************
   update USERS with profile_photo
************/
UPDATE User_ SET profile_photo = 1 WHERE id = 1;
UPDATE User_ SET profile_photo = 2 WHERE id = 2;
UPDATE User_ SET profile_photo = 3 WHERE id = 3;
UPDATE User_ SET profile_photo = 4 WHERE id = 4;
UPDATE User_ SET profile_photo = 5 WHERE id = 5;
UPDATE User_ SET profile_photo = 6 WHERE id = 6;
UPDATE User_ SET profile_photo = 7 WHERE id = 7;
UPDATE User_ SET profile_photo = 8 WHERE id = 8;
UPDATE User_ SET profile_photo = 9 WHERE id = 9;
UPDATE User_ SET profile_photo = 10 WHERE id = 10;
UPDATE User_ SET profile_photo = 11 WHERE id = 11;
UPDATE User_ SET profile_photo = 12 WHERE id = 12;
UPDATE User_ SET profile_photo = 13 WHERE id = 13;
UPDATE User_ SET profile_photo = 14 WHERE id = 14;
UPDATE User_ SET profile_photo = 15 WHERE id = 15;
UPDATE User_ SET profile_photo = 16 WHERE id = 16;
UPDATE User_ SET profile_photo = 17 WHERE id = 17;
UPDATE User_ SET profile_photo = 18 WHERE id = 18;
UPDATE User_ SET profile_photo = 19 WHERE id = 19;
UPDATE User_ SET profile_photo = 20 WHERE id = 20;
UPDATE User_ SET profile_photo = 21 WHERE id = 21;
UPDATE User_ SET profile_photo = 22 WHERE id = 22;
UPDATE User_ SET profile_photo = 23 WHERE id = 23;

/************
   SERVICES populate
************/
INSERT INTO Service_ (id, freelancer_id, name_, description_, duration, price_per_hour, promotion, category_id) VALUES
/************
   Joana Silva (id 3) services populate
************/
(1, 3, 'Limpeza Doméstica Completa', 'Serviço completo de limpeza de casas incluindo todas as divisões, aspiração, lavagem e desinfecção.', 3, 15.0, 0, 1),
(2, 3, 'Limpeza de Escritórios', 'Limpeza profissional de espaços comerciais com produtos ecológicos e equipamento especializado.', 2, 18.0, 10, 1),
(3, 3, 'Limpeza Pós-Obra', 'Serviço especializado para remoção de resíduos e limpeza profunda após obras ou renovações.', 4, 20.0, 0, 1),
(4, 3, 'Limpeza de Condomínios', 'Serviço regular ou pontual de limpeza de áreas comuns em condomínios e prédios.', 3, 17.0, 5, 1),

/************
   Manuel Costa (id 4) services populate
************/
(5, 4, 'Reparações Elétricas', 'Resolução de problemas elétricos, instalação de lâmpadas, interruptores e tomadas.', 2, 25.0, 0, 2),
(6, 4, 'Montagem de Móveis', 'Montagem profissional de móveis de qualquer marca, incluindo IKEA, Conforama, etc.', 3, 22.0, 15, 2),
(7, 4, 'Reparações de Canalizações', 'Resolução de fugas, substituição de torneiras, desentupimentos e instalações.', 2, 28.0, 0, 2),
(8, 4, 'Pinturas de Interiores', 'Serviço de pintura de interiores com tintas de qualidade e acabamento profissional.', 6, 20.0, 0, 2),

/************
   Sofia Martins (id 5) services populate
************/
(9, 5, 'Manutenção de Jardins', 'Serviço completo de tratamento, corte de relva, poda e manutenção de jardins.', 4, 18.0, 0, 3),
(10, 5, 'Instalação de Sistemas de Rega', 'Instalação de sistemas automáticos de rega para jardins e espaços verdes.', 5, 22.0, 0, 3),
(11, 5, 'Paisagismo', 'Design e implementação de projetos de paisagismo para espaços exteriores.', 8, 30.0, 10, 3),
(12, 5, 'Limpeza de Exteriores', 'Limpeza de pátios, terraços e áreas exteriores com equipamento de pressão.', 3, 19.0, 0, 3),

/************
   António Ferreira (id 6) services populate
************/
(13, 6, 'Explicações de Matemática', 'Aulas particulares de matemática para alunos do ensino básico e secundário.', 1, 20.0, 0, 4),
(14, 6, 'Curso de Programação', 'Introdução à programação com Python, Java ou C++ para iniciantes.', 2, 25.0, 5, 4),
(15, 6, 'Aulas de Inglês', 'Aulas individuais de inglês para todos os níveis, com foco em conversação.', 1, 22.0, 0, 4),
(16, 6, 'Preparação para Exames', 'Sessões intensivas de preparação para exames nacionais de várias disciplinas.', 2, 28.0, 0, 4),

/************
   Ana Rodrigues (id 7) services populate
************/
(17, 7, 'Design de Websites', 'Criação de websites responsivos e modernos para empresas e particulares.', 10, 35.0, 0, 5),
(18, 7, 'Suporte Informático', 'Resolução de problemas informáticos, instalação de software e otimização.', 2, 25.0, 10, 5),
(19, 7, 'Design Gráfico', 'Criação de logotipos, cartões de visita, flyers e material promocional.', 5, 30.0, 0, 5),
(20, 7, 'Gestão de Redes Sociais', 'Gestão profissional de contas de redes sociais para empresas.', 5, 28.0, 15, 5),

/************
   João Oliveira (id 8) services populate
************/
(21, 8, 'Manicure e Pedicure', 'Tratamento completo de unhas, incluindo verniz gel e decorações.', 2, 20.0, 0, 6),
(22, 8, 'Corte e Styling de Cabelo', 'Serviços de cabeleireiro com as últimas tendências de corte e penteado.', 1, 25.0, 5, 6),
(23, 8, 'Maquilhagem Profissional', 'Maquilhagem para eventos especiais, casamentos ou sessões fotográficas.', 1, 30.0, 0, 6),
(24, 8, 'Massagem Terapêutica', 'Massagem relaxante ou terapêutica para alívio de stress e tensão muscular.', 1, 35.0, 10, 6),

/************
   Mariana Santos (id 9) services populate
************/
(25, 9, 'Transporte de Mercadorias', 'Serviço de transporte de mercadorias pequenas e médias dentro da cidade.', 2, 22.0, 0, 7),
(26, 9, 'Mudanças Residenciais', 'Serviço completo de mudanças incluindo embalagem, transporte e montagem.', 5, 30.0, 5, 7),
(27, 9, 'Entregas Urgentes', 'Serviço de entregas rápidas para documentos e encomendas pequenas.', 1, 18.0, 0, 7),
(28, 9, 'Transporte de Móveis', 'Transporte especializado de móveis e objetos delicados ou volumosos.', 3, 25.0, 0, 7),

/************
   Ricardo Sousa (id 10) services populate
************/
(29, 10, 'Chef ao Domicílio', 'Serviço de chef privado para jantares especiais e eventos em casa.', 4, 40.0, 0, 8),
(30, 10, 'Aulas de Culinária', 'Aulas práticas de culinária internacional em sua casa.', 2, 30.0, 10, 8),
(31, 10, 'Catering para Eventos', 'Serviço de catering completo para festas e eventos até 50 pessoas.', 6, 35.0, 5, 8),
(32, 10, 'Preparação de Refeições Semanais', 'Preparação de refeições saudáveis para toda a semana, prontas a aquecer.', 3, 28.0, 0, 8),

/************
   Teresa Gomes (id 11) services populate
************/
(33, 11, 'Passeio de Cães', 'Serviço de passeio diário para cães com duração à escolha.', 1, 15.0, 0, 9),
(34, 11, 'Pet Sitting', 'Cuidado de animais em sua casa durante ausências dos donos.', 2, 18.0, 5, 9),
(35, 11, 'Treino Básico de Obediência', 'Sessões de treino para cães com foco em comandos básicos.', 1, 25.0, 0, 9),
(36, 11, 'Grooming para Animais', 'Serviço completo de banho, corte de pelo e arranjo para cães e gatos.', 2, 30.0, 10, 9),

/************
   Category 10 services populate
************/
(37, 3, 'Organização de Documentos', 'Organização e classificação de documentos para empresas e particulares.', 3, 20.0, 0, 10),
(38, 7, 'Criação de Apresentações', 'Desenvolvimento de apresentações profissionais para reuniões e eventos.', 4, 25.0, 5, 10),
(39, 10, 'Gestão de Emails', 'Organização e resposta a emails para profissionais ocupados.', 2, 22.0, 0, 10),
(40, 11, 'Transcrição de Áudio', 'Transcrição profissional de entrevistas, reuniões e outros conteúdos de áudio.', 3, 18.0, 0, 10);

/************
   MEDIA populate - Services Photos
************/
INSERT INTO Media (service_id, path_, title) VALUES
/************
   Service 1 media (2 images)
************/
(1, '/images/services/limpeza_completa1.jpg', 'Limpeza de sala'),
(1, '/images/services/limpeza_completa2.jpg', 'Limpeza de cozinha'),

/************
   Service 2 media (2 images)
************/
(2, '/images/services/limpeza_escritorio1.jpg', 'Escritório antes'),
(2, '/images/services/limpeza_escritorio2.jpg', 'Escritório depois'),

/************
   Service 3 media (1 images)
************/
(3, '/images/services/limpeza_obra.jpg', 'Limpeza pós-obra'),

/************
   Service 4 media (1 images)
************/
(4, '/images/services/limpeza_condominio.jpg', 'Limpeza de hall'),

/************
   Service 5 media (2 images)
************/
(5, '/images/services/eletrica1.jpg', 'Reparação de quadro elétrico'),
(5, '/images/services/eletrica2.jpg', 'Instalação de iluminação'),

/************
   Service 6 media (1 images)
************/
(6, '/images/services/montagem_moveis.jpg', 'Montagem de armário'),

/************
   Service 7 media (1 images)
************/
(7, '/images/services/canalizacao.jpg', 'Reparação de torneira'),

/************
   Service 8 media (2 images)
************/
(8, '/images/services/pintura1.jpg', 'Pintura de sala'),
(8, '/images/services/pintura2.jpg', 'Pintura de quarto'),

/************
   Service 9 media (2 images)
************/
(9, '/images/services/jardim1.jpg', 'Jardinagem'),
(9, '/images/services/jardim2.jpg', 'Corte de relva'),

/************
   Service 10 media (1 images)
************/
(10, '/images/services/rega.jpg', 'Sistema de rega'),

/************
   Service 11 media (3 images)
************/
(11, '/images/services/paisagismo1.jpg', 'Projeto de jardim'),
(11, '/images/services/paisagismo2.jpg', 'Implementação de paisagismo'),
(11, '/images/services/paisagismo3.jpg', 'Jardim finalizado'),

/************
   Service 12 media (1 images)
************/
(12, '/images/services/exterior.jpg', 'Limpeza de pátio'),

/************
   Service 13 media (1 images)
************/
(13, '/images/services/matematica.jpg', 'Explicações de matemática'),

/************
   Service 14 media (2 images)
************/
(14, '/images/services/programacao1.jpg', 'Aula de programação'),
(14, '/images/services/programacao2.jpg', 'Código de exemplo'),

/************
   Service 15 media (1 images)
************/
(15, '/images/services/ingles.jpg', 'Aula de inglês'),

/************
   Service 16 media (1 images)
************/
(16, '/images/services/exames.jpg', 'Preparação para exames'),

/************
   Service 17 media (2 images)
************/
(17, '/images/services/website1.jpg', 'Design de website responsive'),
(17, '/images/services/website2.jpg', 'Portfolio de websites'),

/************
   Service 18 media (1 images)
************/
(18, '/images/services/informatica.jpg', 'Suporte informático'),

/************
   Service 19 media (3 images)
************/
(19, '/images/services/design1.jpg', 'Design de logotipo'),
(19, '/images/services/design2.jpg', 'Design de cartão de visita'),
(19, '/images/services/design3.jpg', 'Design de folheto'),

/************
   Service 20 media (1 images)
************/
(20, '/images/services/redessociais.jpg', 'Gestão de redes sociais'),

/************
   Services 21-39 media (1 image each)
************/
(21, '/images/services/manicure.jpg', 'Manicure e pedicure'),
(22, '/images/services/cabelo.jpg', 'Corte de cabelo'),
(23, '/images/services/maquilhagem.jpg', 'Maquilhagem profissional'),
(24, '/images/services/massagem.jpg', 'Massagem terapêutica'),
(25, '/images/services/transporte.jpg', 'Transporte de mercadorias'),
(26, '/images/services/mudancas.jpg', 'Mudanças residenciais'),
(27, '/images/services/entregas.jpg', 'Entregas urgentes'),
(28, '/images/services/moveis.jpg', 'Transporte de móveis'),
(29, '/images/services/chef.jpg', 'Chef ao domicílio'),
(30, '/images/services/aulas_culinaria.jpg', 'Aulas de culinária'),
(31, '/images/services/catering.jpg', 'Catering para eventos'),
(32, '/images/services/refeicoes.jpg', 'Preparação de refeições'),
(33, '/images/services/passeio_caes.jpg', 'Passeio de cães'),
(34, '/images/services/petsitting.jpg', 'Pet sitting'),
(35, '/images/services/treino_caes.jpg', 'Treino de obediência'),
(36, '/images/services/grooming.jpg', 'Grooming para animais'),
(37, '/images/services/documentos.jpg', 'Organização de documentos'),
(38, '/images/services/apresentacoes.jpg', 'Criação de apresentações'),
(39, '/images/services/emails.jpg', 'Gestão de emails');
-- Service 40 has no media

/************
   PRIME populate
************/
INSERT INTO Prime (user_id, start_date) VALUES
(12, '2024-03-01'),
(13, '2024-03-10'),
(16, '2024-02-15'),
(19, '2024-03-05'),
(21, '2024-02-25');

/************
   PAYMENT populate
************/
INSERT INTO Payment (user_id, card_num, exp_month, exp_year, code_) VALUES
(3, '4532654789123456', 6, 2025, '123'),
(4, '5426789321654987', 8, 2026, '456'),
(5, '4916378245691234', 3, 2025, '789'),
(6, '5678123456789123', 5, 2025, '234'),
(7, '4532987456321789', 7, 2026, '567'),
(12, '5496781234567890', 9, 2026, '345'),
(13, '4716385274169852', 11, 2025, '678'),
(14, '5284761953824671', 2, 2026, '912'),
(15, '4539871265498732', 4, 2025, '345'),
(16, '5123647859321647', 6, 2026, '678'),
(17, '4987654321987654', 8, 2025, '891'),
(18, '5321789456123789', 10, 2026, '234'),
(19, '4123698745632159', 12, 2025, '567'),
(20, '5789456123789456', 1, 2026, '890'),
(21, '4159753852963741', 3, 2025, '123'),
(22, '5963741852963741', 5, 2026, '456'),
(23, '4753159852753159', 7, 2025, '789');

/************
   ADRESS populate
************/
INSERT INTO Address_ (user_id, street, door_num, floor_, district, municipality, zip_code) VALUES
(3, 'Rua das Flores', '123', '2º Esq', 'Porto', 'Porto', '4000-001'),
(4, 'Avenida da Liberdade', '456', '3º Dto', 'Porto', 'Porto', '4000-002'),
(5, 'Rua do Comércio', '78', '1º', 'Porto', 'Porto', '4000-003'),
(6, 'Praça da República', '45', '5º A', 'Porto', 'Porto', '4000-004'),
(7, 'Avenida dos Aliados', '267', '4º', 'Porto', 'Porto', '4000-005'),
(8, 'Rua Santa Catarina', '198', '2º', 'Porto', 'Porto', '4000-006'),
(9, 'Rua Formosa', '56', '3º Esq', 'Porto', 'Porto', '4000-007'),
(10, 'Rua Sá da Bandeira', '432', '1º Dto', 'Porto', 'Porto', '4000-008'),
(11, 'Avenida da Boavista', '789', '6º B', 'Porto', 'Porto', '4000-009'),
(12, 'Rua Cedofeita', '123', '2º', 'Porto', 'Porto', '4000-010'),
(13, 'Rua do Rosário', '45', '1º', 'Matosinhos', 'Matosinhos', '4450-001'),
(14, 'Avenida Brasil', '87', '3º C', 'Matosinhos', 'Matosinhos', '4450-002'),
(15, 'Rua Roberto Ivens', '234', '2º Dto', 'Matosinhos', 'Matosinhos', '4450-003'),
(16, 'Avenida D. Afonso Henriques', '56', '4º Esq', 'Gaia', 'Vila Nova de Gaia', '4400-001'),
(17, 'Rua Cândido dos Reis', '76', '3º', 'Gaia', 'Vila Nova de Gaia', '4400-002'),
(18, 'Avenida da República', '543', '5º', 'Gaia', 'Vila Nova de Gaia', '4400-003'),
(19, 'Rua das Carmelitas', '29', '1º Esq', 'Porto', 'Porto', '4000-011'),
(20, 'Avenida Fernão Magalhães', '345', '2º Dto', 'Porto', 'Porto', '4000-012'),
(21, 'Rua Costa Cabral', '567', '3º', 'Porto', 'Porto', '4000-013'),
(22, 'Avenida Rodrigues de Freitas', '78', '4º Esq', 'Porto', 'Porto', '4000-014'),
(23, 'Rua Miguel Bombarda', '90', '2º', 'Porto', 'Porto', '4000-015');

/************
   Block 2 users for testing
************/
UPDATE User_ SET is_blocked = TRUE WHERE id IN (15, 22);

/************
   REASON_BLOCK populate
************/
INSERT INTO Reason_Block (user_id, reason, extra_info) VALUES
(15, 'Comportamento abusivo ou inapropriado', 'Mensagens ofensivas a freelancers'),
(22, 'Violação das regras da plataforma', 'Tentativa de pagamento fora da plataforma');

/************
   UNBLOCK_APPEAL populate
************/
INSERT INTO Unblock_Appeal (user_id, title, body_, date_, status_) VALUES
(15, 'Pedido de Desbloqueio', 'Peço desculpa pelo comportamento inapropriado. Prometo respeitar as regras da plataforma daqui em diante.', '2024-03-15', 'pending'),
(22, 'Recurso de Bloqueio', 'Houve um mal-entendido quanto ao método de pagamento. Não tinha intenção de violar regras.', '2024-03-16', 'pending');

/************
   SERVICE_DATA populate
************/
/************
   Carlos Almeida (id 12) will have 8 services contracted
************/
INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(12, 1, '2024-01-10', '10:00:00', 5, 'completed', 45.0),
(12, 5, '2024-01-15', '14:00:00', 3, 'completed', 50.0),
(12, 9, '2024-01-20', '09:00:00', 7, 'completed', 72.0),
(12, 13, '2024-02-05', '16:00:00', 0, 'completed', 20.0),
(12, 17, '2024-02-10', '11:00:00', 5, 'completed', 350.0),
(12, 21, '2024-02-20', '15:00:00', 0, 'completed', 40.0),
(12, 29, '2024-03-01', '19:00:00', 10, 'completed', 160.0),
(12, 33, '2024-03-10', '08:00:00', 5, 'paid', 15.0);

/************
   Isabel Pinto (id 13) will have 10 services contracted
************/
INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(13, 2, '2024-01-12', '11:00:00', 0, 'completed', 32.4),
(13, 6, '2024-01-18', '13:00:00', 5, 'completed', 56.1),
(13, 14, '2024-02-02', '17:00:00', 0, 'completed', 47.5),
(13, 26, '2024-02-15', '10:00:00', 15, 'completed', 142.5),
(13, 34, '2024-03-05', '09:00:00', 7, 'paid', 34.2);

INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(13, 7, '2024-01-10', '14:30:00', 5, 'completed', 56.0),
(13, 15, '2024-01-20', '16:30:00', 0, 'completed', 22.0),
(13, 23, '2024-02-08', '17:30:00', 0, 'completed', 30.0),
(13, 31, '2024-02-18', '18:30:00', 15, 'completed', 199.5),
(13, 39, '2024-03-02', '10:30:00', 0, 'accepted', 44.0);

/************
   Paulo Neves (id 14) will have 10 services contracted
************/
INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(14, 3, '2024-01-14', '09:00:00', 10, 'completed', 80.0),
(14, 7, '2024-01-25', '14:00:00', 5, 'completed', 56.0),
(14, 15, '2024-02-07', '16:00:00', 0, 'completed', 22.0),
(14, 27, '2024-02-22', '11:00:00', 8, 'completed', 18.0),
(14, 35, '2024-03-08', '10:00:00', 5, 'paid', 25.0);

INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(14, 8, '2024-01-12', '11:30:00', 10, 'completed', 120.0),
(14, 16, '2024-01-22', '15:30:00', 0, 'completed', 56.0),
(14, 24, '2024-02-10', '16:30:00', 0, 'completed', 31.5),
(14, 32, '2024-02-20', '19:30:00', 10, 'completed', 84.0),
(14, 40, '2024-03-04', '14:30:00', 0, 'accepted', 54.0);

/************
   Diogo Rocha (id 16) will have 11 services contracted
************/
INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(16, 4, '2024-01-16', '13:00:00', 5, 'completed', 48.5),
(16, 8, '2024-01-28', '10:00:00', 10, 'completed', 120.0),
(16, 16, '2024-02-09', '15:00:00', 0, 'completed', 56.0),
(16, 22, '2024-02-25', '14:00:00', 0, 'completed', 23.75),
(16, 30, '2024-03-03', '12:00:00', 10, 'completed', 54.0),
(16, 36, '2024-03-12', '11:00:00', 8, 'paid', 54.0);

INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(16, 1, '2024-01-14', '10:30:00', 5, 'completed', 45.0),
(16, 9, '2024-01-24', '09:30:00', 8, 'completed', 72.0),
(16, 17, '2024-02-12', '11:30:00', 5, 'completed', 350.0),
(16, 25, '2024-02-22', '12:30:00', 10, 'completed', 44.0),
(16, 33, '2024-03-06', '08:30:00', 5, 'accepted', 15.0);

/************
   Catarina Mota (id 17) will have 10 services contracted
************/
INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(17, 1, '2024-01-17', '14:00:00', 5, 'completed', 45.0),
(17, 9, '2024-01-30', '09:00:00', 8, 'completed', 72.0),
(17, 17, '2024-02-12', '13:00:00', 5, 'completed', 350.0),
(17, 25, '2024-02-27', '11:00:00', 10, 'completed', 44.0),
(17, 33, '2024-03-07', '10:00:00', 5, 'paid', 15.0);

INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(17, 2, '2024-01-16', '13:30:00', 0, 'completed', 32.4),
(17, 10, '2024-01-26', '14:30:00', 10, 'completed', 110.0),
(17, 18, '2024-02-14', '16:30:00', 0, 'completed', 45.0),
(17, 26, '2024-02-24', '10:30:00', 15, 'completed', 142.5),
(17, 34, '2024-03-08', '09:30:00', 7, 'accepted', 34.2);

/************
   Tiago Lopes (id 18) will have 5 services contracted
************/
INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(18, 2, '2024-01-19', '10:00:00', 0, 'completed', 32.4),
(18, 10, '2024-02-01', '14:00:00', 10, 'completed', 110.0),
(18, 18, '2024-02-14', '11:00:00', 0, 'completed', 45.0),
(18, 26, '2024-03-01', '09:00:00', 15, 'completed', 142.5),
(18, 34, '2024-03-09', '13:00:00', 7, 'paid', 34.2);

/************
   Marta Ribeiro (id 19) will have 6 services contracted
************/
INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(19, 3, '2024-01-22', '11:00:00', 10, 'completed', 80.0),
(19, 11, '2024-02-03', '15:00:00', 12, 'completed', 216.0),
(19, 19, '2024-02-16', '14:00:00', 5, 'completed', 150.0),
(19, 23, '2024-02-28', '10:00:00', 0, 'completed', 30.0),
(19, 31, '2024-03-04', '18:00:00', 15, 'completed', 199.5),
(19, 37, '2024-03-13', '14:00:00', 8, 'paid', 60.0);

/************
   Bruno Cunha (id 20) will have 10 services contracted
************/
INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(20, 4, '2024-01-24', '09:00:00', 5, 'completed', 48.5),
(20, 12, '2024-02-05', '13:00:00', 7, 'completed', 57.0),
(20, 20, '2024-02-18', '16:00:00', 0, 'completed', 119.0),
(20, 28, '2024-03-02', '11:00:00', 12, 'completed', 75.0),
(20, 32, '2024-03-10', '19:00:00', 10, 'paid', 84.0);

INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(20, 5, '2024-01-22', '17:30:00', 3, 'completed', 50.0),
(20, 13, '2024-02-01', '10:30:00', 0, 'completed', 20.0),
(20, 21, '2024-02-20', '15:30:00', 0, 'completed', 40.0),
(20, 29, '2024-03-01', '19:30:00', 10, 'completed', 160.0),
(20, 37, '2024-03-14', '14:30:00', 8, 'accepted', 60.0);

/************
   Andreia Moreira (id 21) will have 7 services contracted
************/
INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(21, 5, '2024-01-26', '14:00:00', 3, 'completed', 50.0),
(21, 13, '2024-02-07', '10:00:00', 0, 'completed', 20.0),
(21, 21, '2024-02-19', '15:00:00', 0, 'completed', 40.0),
(21, 29, '2024-03-03', '19:00:00', 10, 'completed', 160.0),
(21, 35, '2024-03-11', '10:00:00', 5, 'paid', 25.0);

INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(21, 6, '2024-01-24', '11:30:00', 5, 'completed', 56.1),
(21, 14, '2024-02-03', '14:30:00', 0, 'completed', 47.5);

/************
   Raquel Dias (id 23) will have 5 services contracted
************/
INSERT INTO Service_Data (user_id, service_id, date_, time_, travel_fee, status_, final_price) VALUES
(23, 6, '2024-01-28', '11:00:00', 5, 'completed', 56.1),
(23, 14, '2024-02-09', '14:00:00', 0, 'completed', 47.5),
(23, 22, '2024-02-21', '16:00:00', 0, 'completed', 23.75),
(23, 30, '2024-03-05', '12:00:00', 10, 'completed', 54.0),
(23, 36, '2024-03-14', '11:00:00', 8, 'paid', 54.0);

/************
   FEEDBACK populate
************/
/************
   Feedback for services provided by Joana Silva (id 3)
************/
INSERT INTO Feedback (user_id, service_id, title, description_, evaluation, date_) VALUES
(12, 1, 'Excelente serviço', 'A Joana fez um trabalho impecável na limpeza da minha casa. Tudo ficou brilhante!', 5.0, '2024-01-12'),
(13, 2, 'Muito profissional', 'Limpeza de escritório realizada com grande profissionalismo e atenção aos detalhes.', 4.5, '2024-01-14'),
(14, 3, 'Eficiente e rápido', 'Conseguiu eliminar toda a sujidade da obra em tempo recorde. Recomendo!', 5.0, '2024-01-16'),
(16, 4, 'Bom trabalho', 'Fez um bom trabalho na limpeza do condomínio, mas poderia ter sido mais detalhada.', 4.0, '2024-01-18'),
(17, 1, 'Serviço de qualidade', 'A Joana é muito profissional e deixou a casa impecável. Voltarei a contratar!', 4.5, '2024-01-19'),
(18, 2, 'Excelente relação qualidade/preço', 'Serviço rápido e eficiente a um preço justo. Recomendo vivamente.', 5.0, '2024-01-21'),
(16, 1, 'Não cumpriu o horário', 'O serviço foi bem executado, mas chegou 30 minutos atrasada.', 3.5, '2024-01-16');

/************
   Feedback for services provided by Manuel Costa (id 4)
************/
INSERT INTO Feedback (user_id, service_id, title, description_, evaluation, date_) VALUES
(12, 5, 'Resolveu o problema rapidamente', 'Problema elétrico complexo resolvido em pouco tempo. Muito competente!', 5.0, '2024-01-17'),
(13, 6, 'Montagem perfeita', 'Montou todos os móveis com precisão e cuidado. Excelente profissional!', 5.0, '2024-01-20'),
(14, 7, 'Reparação eficiente', 'Conseguiu reparar uma fuga de água complicada sem grandes transtornos.', 4.5, '2024-01-27'),
(16, 8, 'Pintura de qualidade', 'Trabalho de pintura meticuloso e com excelente acabamento.', 5.0, '2024-01-30'),
(23, 6, 'Bom serviço', 'Montou os móveis corretamente, mas deixou alguma sujidade.', 4.0, '2024-01-30');

/************
   Feedback for services provided by Sofia Martins (id 5)
************/
INSERT INTO Feedback (user_id, service_id, title, description_, evaluation, date_) VALUES
(12, 9, 'Jardim transformado', 'A Sofia conseguiu dar uma nova vida ao meu jardim. Está lindíssimo!', 5.0, '2024-01-22'),
(17, 9, 'Manutenção excelente', 'Serviço de manutenção do jardim realizado com grande profissionalismo.', 4.5, '2024-02-01'),
(18, 10, 'Sistema funcional', 'Instalou um sistema de rega eficiente e fácil de usar.', 4.0, '2024-02-03'),
(19, 11, 'Projeto incrível', 'O projeto de paisagismo superou todas as minhas expectativas!', 5.0, '2024-02-05'),
(20, 12, 'Limpeza eficaz', 'Limpeza do terraço muito bem feita, parece novo!', 4.5, '2024-02-07');

/************
   Feedback for services provided by António Ferreira (id 6)
************/
INSERT INTO Feedback (user_id, service_id, title, description_, evaluation, date_) VALUES
(12, 13, 'Aulas esclarecedoras', 'As explicações do António ajudaram muito o meu filho a melhorar as notas.', 5.0, '2024-02-07'),
(13, 14, 'Curso muito útil', 'Aprendi os fundamentos da programação de forma clara e prática.', 4.5, '2024-02-04'),
(14, 15, 'Professor paciente', 'Excelente método de ensino e muita paciência para explicar.', 5.0, '2024-02-09'),
(16, 16, 'Preparação eficaz', 'A preparação para o exame foi fundamental para o sucesso do meu filho.', 5.0, '2024-02-11'),
(21, 13, 'Muito conhecedor', 'Domina completamente a matéria e sabe transmitir o conhecimento.', 4.5, '2024-02-09');

/************
   Feedback for services provided by Ana Rodrigues (id 7)
************/
INSERT INTO Feedback (user_id, service_id, title, description_, evaluation, date_) VALUES
(12, 17, 'Website incrível', 'Criou um website moderno e funcional para o meu negócio. Estou muito satisfeito!', 5.0, '2024-02-12'),
(17, 17, 'Design profissional', 'O website ficou com um design elegante e profissional. Recomendo!', 4.5, '2024-02-14'),
(18, 18, 'Problema resolvido', 'Resolveu rapidamente um problema informático que me estava a dar muitas dores de cabeça.', 5.0, '2024-02-16'),
(19, 19, 'Logo perfeito', 'Criou um logotipo que representa perfeitamente a identidade da minha marca.', 5.0, '2024-02-18'),
(20, 20, 'Gestão eficiente', 'A gestão das redes sociais melhorou significativamente a visibilidade do meu negócio.', 4.5, '2024-02-20');

/************
   Feedback for services provided by João Oliveira (id 8)
************/
INSERT INTO Feedback (user_id, service_id, title, description_, evaluation, date_) VALUES
(16, 22, 'Corte moderno', 'Adorei o corte de cabelo! Ficou exatamente como eu queria.', 5.0, '2024-02-27'),
(19, 23, 'Maquilhagem para evento', 'A maquilhagem para o meu evento ficou deslumbrante e durou toda a noite.', 4.5, '2024-03-01'),
(18, 22, 'Bom atendimento', 'Profissional atencioso e com boas técnicas de corte.', 4.0, '2024-02-23');

/************
   Feedback for services provided by Mariana Santos (id 9)
************/
INSERT INTO Feedback (user_id, service_id, title, description_, evaluation, date_) VALUES
(17, 25, 'Entrega pontual', 'Transporte realizado com pontualidade e cuidado com as mercadorias.', 4.5, '2024-02-29'),
(18, 26, 'Mudança sem stress', 'Toda a mudança decorreu sem problemas, com muito profissionalismo.', 5.0, '2024-03-03'),
(19, 27, 'Rápido e seguro', 'Entrega realizada rapidamente e com toda a segurança.', 4.5, '2024-02-29'),
(20, 28, 'Cuidado com os móveis', 'Transportou os móveis com muito cuidado, sem qualquer dano.', 5.0, '2024-03-04');

/************
   Feedback for services provided by Ricardo Sousa (id 10)
************/
INSERT INTO Feedback (user_id, service_id, title, description_, evaluation, date_) VALUES
(12, 29, 'Jantar memorável', 'O chef preparou um jantar incrível que impressionou todos os convidados!', 5.0, '2024-03-03'),
(21, 29, 'Experiência gastronómica', 'Uma experiência culinária fantástica! Recomendo vivamente.', 5.0, '2024-03-05'),
(13, 30, 'Aulas dinâmicas', 'Aprendi técnicas de culinária muito úteis de forma divertida.', 4.5, '2024-03-07'),
(19, 31, 'Catering perfeito', 'O serviço de catering para o meu evento foi um sucesso absoluto!', 5.0, '2024-03-06'),
(20, 32, 'Refeições deliciosas', 'As refeições preparadas são saborosas e nutritivas.', 4.5, '2024-03-12'),
(23, 30, 'Muito didático', 'Explica as técnicas culinárias de forma clara e acessível.', 5.0, '2024-03-07');

/************
   Feedback for services provided by Teresa Gomes (id 11)
************/
INSERT INTO Feedback (user_id, service_id, title, description_, evaluation, date_) VALUES
(12, 33, 'Cão feliz', 'O meu cão adora os passeios com a Teresa! Volta sempre cansado e feliz.', 5.0, '2024-03-12'),
(17, 33, 'Profissional atenciosa', 'Muito atenciosa com o meu animal de estimação durante os passeios.', 4.5, '2024-03-09'),
(14, 35, 'Resultados visíveis', 'O treino de obediência já está a mostrar resultados positivos!', 4.5, '2024-03-10'),
(16, 36, 'Grooming de qualidade', 'O meu cão ficou lindo e cheiroso após o grooming.', 5.0, '2024-03-14'),
(21, 35, 'Métodos eficazes', 'Os métodos de treino são eficazes e respeitam o bem-estar do animal.', 5.0, '2024-03-13'),
(23, 36, 'Profissionalismo', 'Serviço de grooming realizado com muito profissionalismo e carinho.', 4.5, '2024-03-16');

/************
   Other Feedbacks
************/
INSERT INTO Feedback (user_id, service_id, title, description_, evaluation, date_) VALUES
(12, 1, 'Segunda contratação', 'Contratei novamente e o serviço foi ainda melhor que da primeira vez.', 5.0, '2024-03-15'),
(13, 2, 'Cliente regular', 'Continuarei a contratar este serviço mensalmente, excelente qualidade.', 5.0, '2024-03-16'),
(14, 3, 'Superou expectativas', 'Conseguiu limpar áreas que eu achava que ficariam permanentemente manchadas.', 4.5, '2024-03-17'),
(16, 4, 'Melhoria visível', 'O serviço melhorou significativamente desde a última vez.', 4.5, '2024-03-18'),
(14, 7, 'Problema resolvido permanentemente', 'A fuga foi completamente eliminada sem reincidências.', 5.0, '2024-03-10'),
(16, 8, 'Acabamento perfeito', 'A pintura tem um acabamento profissional que valoriza o imóvel.', 5.0, '2024-03-11'),
(18, 10, 'Sistema eficiente', 'O sistema de rega instalado é muito eficiente e poupa água.', 4.5, '2024-03-12'),
(19, 11, 'Jardim admirado', 'Todos os vizinhos admiram o novo design do jardim.', 5.0, '2024-03-13'),
(13, 14, 'Aprendizagem rápida', 'Consegui aprender mais em poucas aulas do que em meses por conta própria.', 5.0, '2024-03-14'),
(19, 19, 'Material promocional completo', 'Criou toda a identidade visual da minha empresa com coerência e qualidade.', 5.0, '2024-03-15');

/************
   MESSAGE populate
************/
INSERT INTO Message_ (sender_id, receiver_id, body_, date_, time_, is_read) VALUES
/************
   Message entre User 12 e Freelancer 3
************/
(12, 3, 'Olá, gostaria de marcar uma limpeza doméstica para a próxima semana.', '2024-01-08', '09:15:00', TRUE),
(3, 12, 'Bom dia! Claro, tenho disponibilidade na terça-feira. Que horas prefere?', '2024-01-08', '10:30:00', TRUE),
(12, 3, 'Terça às 10h seria perfeito. A casa tem 3 quartos e 2 casas de banho.', '2024-01-08', '11:45:00', TRUE),
(3, 12, 'Perfeito, está marcado para terça às 10h. Prevejo cerca de 3 horas para o serviço completo.', '2024-01-08', '14:20:00', TRUE),

/************
   Message entre User 13 e Freelancer 4
************/
(13, 4, 'Preciso urgentemente de uma reparação na canalização da cozinha. Quando poderia vir?', '2024-01-16', '08:05:00', TRUE),
(4, 13, 'Bom dia. Posso passar hoje à tarde, por volta das 15h. É uma boa hora?', '2024-01-16', '09:10:00', TRUE),
(13, 4, 'Sim, 15h está ótimo. O problema é uma fuga debaixo do lava-loiça.', '2024-01-16', '09:30:00', TRUE),
(4, 13, 'Entendido. Estarei aí às 15h com as ferramentas necessárias.', '2024-01-16', '10:00:00', TRUE),

/************
   Message entre User 14 e Freelancer 5
************/
(14, 5, 'Olá Sofia, gostaria de um orçamento para manutenção do meu jardim de 100m².', '2024-01-22', '11:20:00', TRUE),
(5, 14, 'Olá! Para um jardim dessa dimensão, o preço seria aproximadamente 70€ por sessão. Podemos agendar uma visita para avaliar melhor?', '2024-01-22', '13:45:00', TRUE),
(14, 5, 'Sim, podemos marcar. Estou disponível amanhã ou depois de amanhã de manhã.', '2024-01-22', '14:30:00', TRUE),
(5, 14, 'Ótimo! Posso passar amanhã às 10h para vermos o jardim e discutir os detalhes.', '2024-01-22', '15:15:00', TRUE),

/************
   Message entre User 16 e Freelancer 6
************/
(16, 6, 'Procuro explicações de matemática para o meu filho de 15 anos que está a preparar-se para os exames nacionais.', '2024-01-25', '18:00:00', TRUE),
(6, 16, 'Boa tarde! Tenho experiência com preparação para exames nacionais. Que áreas da matemática ele precisa de mais ajuda?', '2024-01-25', '19:10:00', TRUE),
(16, 6, 'Principalmente funções e trigonometria. Quando poderia começar?', '2024-01-25', '20:05:00', TRUE),
(6, 16, 'Posso começar na próxima semana. Terça ou quinta-feira às 17h seria possível?', '2024-01-25', '21:15:00', TRUE),
(16, 6, 'Quinta-feira às 17h é perfeito. Obrigado!', '2024-01-26', '09:30:00', TRUE),

/************
   Message entre User 17 e Freelancer 7
************/
(17, 7, 'Preciso de um website para o meu novo negócio de artesanato. Pode ajudar?', '2024-02-05', '10:25:00', TRUE),
(7, 17, 'Olá! Claro que posso ajudar. Que funcionalidades gostaria que o website tivesse?', '2024-02-05', '11:40:00', TRUE),
(17, 7, 'Preciso de uma página inicial, galeria de produtos e um formulário de contacto. Também gostaria de uma loja online básica.', '2024-02-05', '13:20:00', TRUE),
(7, 17, 'Perfeito, posso criar isso para si. Podemos marcar uma reunião para discutir os detalhes do design e conteúdo?', '2024-02-05', '14:35:00', TRUE),
(17, 7, 'Sim, estou disponível na quarta ou sexta-feira à tarde.', '2024-02-05', '15:50:00', TRUE),

/************
   Message entre User 18 e Freelancer 8
************/
(18, 8, 'Gostaria de marcar um serviço de corte de cabelo e coloração para esta semana.', '2024-02-15', '09:15:00', TRUE),
(8, 18, 'Bom dia! Tenho disponibilidade na quinta-feira às 14h ou na sexta às 10h. Qual prefere?', '2024-02-15', '10:30:00', TRUE),
(18, 8, 'Sexta às 10h seria melhor para mim. Quero um corte curto e uma coloração em tons de castanho.', '2024-02-15', '11:45:00', TRUE),
(8, 18, 'Perfeito, está marcado para sexta às 10h. Prevejo cerca de 2h para o serviço completo.', '2024-02-15', '13:00:00', TRUE),

/************
   Message entre User 19 e Freelancer 9
************/
(19, 9, 'Preciso de ajuda para uma mudança de apartamento no próximo mês. O apartamento tem 2 quartos.', '2024-02-20', '16:10:00', TRUE),
(9, 19, 'Olá! Claro que posso ajudar. Quando seria exatamente e entre quais endereços?', '2024-02-20', '17:25:00', TRUE),
(19, 9, 'Seria no dia 15 de março, da Rua das Carmelitas para a Avenida da Boavista, ambos no Porto.', '2024-02-20', '18:40:00', TRUE),
(9, 19, 'Entendido. Posso fazer esse serviço no dia 15. Teria preferência de horário?', '2024-02-20', '19:55:00', TRUE),
(19, 9, 'De manhã seria o ideal, a partir das 9h.', '2024-02-21', '08:30:00', TRUE),

/************
   Message entre User 20 e Freelancer 10
************/
(20, 10, 'Estou a planear um jantar especial para 8 pessoas no próximo sábado. Estaria disponível como chef?', '2024-02-25', '14:00:00', TRUE),
(10, 20, 'Boa tarde! Sim, estou disponível no próximo sábado. Que tipo de menu gostaria?', '2024-02-25', '15:15:00', TRUE),
(20, 10, 'Estou a pensar num menu de 3 pratos, inspirado na gastronomia mediterrânica.', '2024-02-25', '16:30:00', TRUE),
(10, 20, 'Excelente escolha! Posso preparar uma entrada de tapas variados, um prato principal de peixe ou marisco e uma sobremesa tradicional. Seria do seu agrado?', '2024-02-25', '17:45:00', TRUE),
(20, 10, 'Parece perfeito! A que horas precisaria de chegar para preparar tudo?', '2024-02-25', '19:00:00', TRUE),

/************
   Message entre User 21 e Freelancer 11
************/
(21, 11, 'Olá, tenho um Labrador de 2 anos que precisa de treino básico de obediência. Pode ajudar?', '2024-03-01', '11:05:00', TRUE),
(11, 21, 'Olá! Claro que posso ajudar. Os Labradores são muito inteligentes e aprendem rapidamente. Que comportamentos específicos gostaria de trabalhar?', '2024-03-01', '12:20:00', TRUE),
(21, 11, 'Principalmente o chamamento e não puxar a trela durante os passeios.', '2024-03-01', '13:35:00', TRUE),
(11, 21, 'Entendido. Posso começar com sessões semanais de 1 hora. Quando gostaria de começar?', '2024-03-01', '14:50:00', TRUE),
(21, 11, 'Podemos começar na próxima semana? Terça ou quinta-feira seria ideal.', '2024-03-01', '16:05:00', TRUE);

/************
   REQUEST populate
************/
INSERT INTO Request (service_data_id, message_id, title, price, duration) VALUES
(1, 1, 'Pedido de Limpeza Doméstica', 45.0, 3),
(5, 5, 'Pedido de Reparação Elétrica', 50.0, 2),
(9, 9, 'Pedido de Manutenção de Jardim', 72.0, 4),
(13, 13, 'Pedido de Explicações de Matemática', 20.0, 1),
(17, 17, 'Pedido de Design de Website', 350.0, 10),
(21, 21, 'Pedido de Manicure e Pedicure', 40.0, 2),
(25, 25, 'Pedido de Transporte de Mercadorias', 44.0, 2),
(29, 29, 'Pedido de Chef ao Domicílio', 160.0, 4),
(33, 33, 'Pedido de Passeio de Cães', 15.0, 1);

/************
   COMPLAINT populate
************/
INSERT INTO Complaint (service_data_id, message_id, admin_id, title, body_, is_accepted) VALUES
(2, 2, 1, 'Serviço incompleto', 'A limpeza do escritório não incluiu a área da copa conforme combinado.', TRUE),
(6, 6, 2, 'Móvel danificado', 'Durante a montagem, uma das portas do armário foi danificada.', FALSE),
(10, 10, 1, 'Sistema de rega com defeito', 'O sistema de rega instalado apresentou falhas logo no primeiro dia.', TRUE),
(14, 14, 2, 'Material desatualizado', 'O material utilizado nas aulas de programação está desatualizado.', FALSE),
(18, 18, 1, 'Problema não resolvido', 'O problema no computador voltou a acontecer no dia seguinte.', TRUE);

COMMIT;