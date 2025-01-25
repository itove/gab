--
-- PostgreSQL database dump
--

-- Dumped from database version 16.3
-- Dumped by pg_dump version 16.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: notify_messenger_messages(); Type: FUNCTION; Schema: public; Owner: gab
--

CREATE FUNCTION public.notify_messenger_messages() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$;


ALTER FUNCTION public.notify_messenger_messages() OWNER TO gab;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: gab
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


ALTER TABLE public.doctrine_migration_versions OWNER TO gab;

--
-- Name: insured; Type: TABLE; Schema: public; Owner: gab
--

CREATE TABLE public.insured (
    id integer NOT NULL,
    school_id integer,
    name character varying(255) NOT NULL,
    idnum character varying(255) NOT NULL,
    grade character varying(255) NOT NULL,
    class character varying(255) NOT NULL
);


ALTER TABLE public.insured OWNER TO gab;

--
-- Name: insured_id_seq; Type: SEQUENCE; Schema: public; Owner: gab
--

CREATE SEQUENCE public.insured_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.insured_id_seq OWNER TO gab;

--
-- Name: insured_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gab
--

ALTER SEQUENCE public.insured_id_seq OWNED BY public.insured.id;


--
-- Name: messenger_messages; Type: TABLE; Schema: public; Owner: gab
--

CREATE TABLE public.messenger_messages (
    id bigint NOT NULL,
    body text NOT NULL,
    headers text NOT NULL,
    queue_name character varying(190) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    available_at timestamp(0) without time zone NOT NULL,
    delivered_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


ALTER TABLE public.messenger_messages OWNER TO gab;

--
-- Name: COLUMN messenger_messages.created_at; Type: COMMENT; Schema: public; Owner: gab
--

COMMENT ON COLUMN public.messenger_messages.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN messenger_messages.available_at; Type: COMMENT; Schema: public; Owner: gab
--

COMMENT ON COLUMN public.messenger_messages.available_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN messenger_messages.delivered_at; Type: COMMENT; Schema: public; Owner: gab
--

COMMENT ON COLUMN public.messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)';


--
-- Name: messenger_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: gab
--

CREATE SEQUENCE public.messenger_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.messenger_messages_id_seq OWNER TO gab;

--
-- Name: messenger_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gab
--

ALTER SEQUENCE public.messenger_messages_id_seq OWNED BY public.messenger_messages.id;


--
-- Name: order; Type: TABLE; Schema: public; Owner: gab
--

CREATE TABLE public."order" (
    id integer NOT NULL,
    applicant_id integer,
    insured_id integer,
    product_id integer
);


ALTER TABLE public."order" OWNER TO gab;

--
-- Name: order_id_seq; Type: SEQUENCE; Schema: public; Owner: gab
--

CREATE SEQUENCE public.order_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.order_id_seq OWNER TO gab;

--
-- Name: order_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gab
--

ALTER SEQUENCE public.order_id_seq OWNED BY public."order".id;


--
-- Name: product; Type: TABLE; Schema: public; Owner: gab
--

CREATE TABLE public.product (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    price integer NOT NULL
);


ALTER TABLE public.product OWNER TO gab;

--
-- Name: product_id_seq; Type: SEQUENCE; Schema: public; Owner: gab
--

CREATE SEQUENCE public.product_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_id_seq OWNER TO gab;

--
-- Name: product_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gab
--

ALTER SEQUENCE public.product_id_seq OWNED BY public.product.id;


--
-- Name: school; Type: TABLE; Schema: public; Owner: gab
--

CREATE TABLE public.school (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    province character varying(255) NOT NULL,
    city character varying(255) NOT NULL,
    area character varying(255) NOT NULL
);


ALTER TABLE public.school OWNER TO gab;

--
-- Name: school_id_seq; Type: SEQUENCE; Schema: public; Owner: gab
--

CREATE SEQUENCE public.school_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.school_id_seq OWNER TO gab;

--
-- Name: school_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gab
--

ALTER SEQUENCE public.school_id_seq OWNED BY public.school.id;


--
-- Name: school_stage; Type: TABLE; Schema: public; Owner: gab
--

CREATE TABLE public.school_stage (
    school_id integer NOT NULL,
    stage_id integer NOT NULL
);


ALTER TABLE public.school_stage OWNER TO gab;

--
-- Name: stage; Type: TABLE; Schema: public; Owner: gab
--

CREATE TABLE public.stage (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    grades text
);


ALTER TABLE public.stage OWNER TO gab;

--
-- Name: COLUMN stage.grades; Type: COMMENT; Schema: public; Owner: gab
--

COMMENT ON COLUMN public.stage.grades IS '(DC2Type:simple_array)';


--
-- Name: stage_id_seq; Type: SEQUENCE; Schema: public; Owner: gab
--

CREATE SEQUENCE public.stage_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.stage_id_seq OWNER TO gab;

--
-- Name: stage_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gab
--

ALTER SEQUENCE public.stage_id_seq OWNED BY public.stage.id;


--
-- Name: user; Type: TABLE; Schema: public; Owner: gab
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    username character varying(180) NOT NULL,
    roles json NOT NULL,
    password character varying(255) NOT NULL,
    name character varying(255) DEFAULT NULL::character varying,
    phone character varying(255) DEFAULT NULL::character varying,
    idnum character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public."user" OWNER TO gab;

--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: gab
--

CREATE SEQUENCE public.user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_id_seq OWNER TO gab;

--
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gab
--

ALTER SEQUENCE public.user_id_seq OWNED BY public."user".id;


--
-- Name: insured id; Type: DEFAULT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.insured ALTER COLUMN id SET DEFAULT nextval('public.insured_id_seq'::regclass);


--
-- Name: messenger_messages id; Type: DEFAULT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.messenger_messages ALTER COLUMN id SET DEFAULT nextval('public.messenger_messages_id_seq'::regclass);


--
-- Name: order id; Type: DEFAULT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public."order" ALTER COLUMN id SET DEFAULT nextval('public.order_id_seq'::regclass);


--
-- Name: product id; Type: DEFAULT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.product ALTER COLUMN id SET DEFAULT nextval('public.product_id_seq'::regclass);


--
-- Name: school id; Type: DEFAULT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.school ALTER COLUMN id SET DEFAULT nextval('public.school_id_seq'::regclass);


--
-- Name: stage id; Type: DEFAULT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.stage ALTER COLUMN id SET DEFAULT nextval('public.stage_id_seq'::regclass);


--
-- Name: user id; Type: DEFAULT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public."user" ALTER COLUMN id SET DEFAULT nextval('public.user_id_seq'::regclass);


--
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: gab
--

COPY public.doctrine_migration_versions (version, executed_at, execution_time) FROM stdin;
DoctrineMigrations\\Version20250125070043	2025-01-25 07:02:31	63
DoctrineMigrations\\Version20250125070327	2025-01-25 07:03:38	7
DoctrineMigrations\\Version20250125071956	2025-01-25 07:20:16	25
DoctrineMigrations\\Version20250125072640	2025-01-25 07:26:47	52
DoctrineMigrations\\Version20250125072834	2025-01-25 07:28:38	19
DoctrineMigrations\\Version20250125080700	2025-01-25 08:07:03	34
DoctrineMigrations\\Version20250125081147	2025-01-25 08:11:54	45
DoctrineMigrations\\Version20250125081303	2025-01-25 08:13:09	11
\.


--
-- Data for Name: insured; Type: TABLE DATA; Schema: public; Owner: gab
--

COPY public.insured (id, school_id, name, idnum, grade, class) FROM stdin;
\.


--
-- Data for Name: messenger_messages; Type: TABLE DATA; Schema: public; Owner: gab
--

COPY public.messenger_messages (id, body, headers, queue_name, created_at, available_at, delivered_at) FROM stdin;
\.


--
-- Data for Name: order; Type: TABLE DATA; Schema: public; Owner: gab
--

COPY public."order" (id, applicant_id, insured_id, product_id) FROM stdin;
\.


--
-- Data for Name: product; Type: TABLE DATA; Schema: public; Owner: gab
--

COPY public.product (id, name, price) FROM stdin;
1	学平险	100
\.


--
-- Data for Name: school; Type: TABLE DATA; Schema: public; Owner: gab
--

COPY public.school (id, name, province, city, area) FROM stdin;
2	松滋市划子嘴初级中学	湖北省	荆州市	松滋市
3	松滋市高成初级中学	湖北省	荆州市	松滋市
4	松滋市实验幼儿园	湖北省	荆州市	松滋市
5	松滋市划子嘴幼儿园	湖北省	荆州市	松滋市
6	松滋市机关幼儿园	湖北省	荆州市	松滋市
7	松滋市高成幼儿园	湖北省	荆州市	松滋市
8	松滋市黄杰小学附属幼儿园	湖北省	荆州市	松滋市
9	实验小学附属幼儿园	湖北省	荆州市	松滋市
10	松滋市特殊教育学校	湖北省	荆州市	松滋市
11	松滋市育苗学校	湖北省	荆州市	松滋市
12	松滋市乐乡街道金松小学	湖北省	荆州市	松滋市
13	松滋市乐乡街道车阳河小学	湖北省	荆州市	松滋市
14	松滋市乐乡街道麻水小学	湖北省	荆州市	松滋市
15	松滋市乐乡街道中心幼儿园	湖北省	荆州市	松滋市
16	松滋市乐乡街道碾盘幼儿园	湖北省	荆州市	松滋市
17	松滋市新江口街道阳光实验幼儿园	湖北省	荆州市	松滋市
18	松滋市新江口街道天天向上幼儿园	湖北省	荆州市	松滋市
19	松滋市新街口街道东方实验幼儿园	湖北省	荆州市	松滋市
20	松滋巿新江口街道灯泡厂幼儿园	湖北省	荆州市	松滋市
21	松滋市新江口街道快乐天使幼儿园	湖北省	荆州市	松滋市
22	松滋市新江口街道小天使幼儿园	湖北省	荆州市	松滋市
23	松滋市新江口街道木天河幼儿园	湖北省	荆州市	松滋市
24	松滋市新江口街道星光幼儿园	湖北省	荆州市	松滋市
25	松滋市新江口街道爱心幼儿园	湖北省	荆州市	松滋市
26	松滋市新江口街道桥东幼儿园	湖北省	荆州市	松滋市
27	杨林市镇初级中学	湖北省	荆州市	松滋市
28	杨林市镇杨林市小学	湖北省	荆州市	松滋市
29	杨林市镇大河北小学	湖北省	荆州市	松滋市
30	杨林市镇台山小学	湖北省	荆州市	松滋市
31	杨林市镇小太阳实验幼儿园	湖北省	荆州市	松滋市
32	杨林市镇中心幼儿园	湖北省	荆州市	松滋市
33	杨林市镇台山幼儿园	湖北省	荆州市	松滋市
34	刘家场镇刘家场初级中学	湖北省	荆州市	松滋市
35	刘家场镇庆贺寺初级中学	湖北省	荆州市	松滋市
36	刘家场镇刘家场小学	湖北省	荆州市	松滋市
37	刘家场镇贺炳炎小学	湖北省	荆州市	松滋市
38	刘家场镇官渡坪小学	湖北省	荆州市	松滋市
39	刘家场镇付家坪小学	湖北省	荆州市	松滋市
40	刘家场镇青坪小学	湖北省	荆州市	松滋市
41	刘家场镇桃树小学	湖北省	荆州市	松滋市
42	刘家场镇观音淌小学	湖北省	荆州市	松滋市
43	刘家场镇机关幼儿园	湖北省	荆州市	松滋市
44	刘家场镇机关幼儿园桃分园	湖北省	荆州市	松滋市
45	刘家场镇启明星幼儿园	湖北省	荆州市	松滋市
46	刘家场镇庆贺寺幼儿园	湖北省	荆州市	松滋市
47	刘家场镇青坪中心幼儿园	湖北省	荆州市	松滋市
48	刘家场镇官渡幼儿园	湖北省	荆州市	松滋市
49	刘家场镇龙潭桥幼儿园	湖北省	荆州市	松滋市
50	松滋市纸厂河镇初级中学	湖北省	荆州市	松滋市
51	松滋市纸厂河镇水府小学	湖北省	荆州市	松滋市
52	纸厂河镇纸厂河小学	湖北省	荆州市	松滋市
53	纸厂河镇金羊山小学	湖北省	荆州市	松滋市
54	纸厂河镇万福幼儿园	湖北省	荆州市	松滋市
55	纸厂河镇金羊山幼儿园	湖北省	荆州市	松滋市
56	纸厂河镇陈家场幼儿园	湖北省	荆州市	松滋市
57	纸厂河镇中心幼儿园	湖北省	荆州市	松滋市
58	松滋市陈店镇陈店初级中学	湖北省	荆州市	松滋市
59	松滋市陈店镇陈店小学	湖北省	荆州市	松滋市
60	松滋市陈店镇桃岭小学	湖北省	荆州市	松滋市
61	松滋市陈店镇陈店小学观岳校区	湖北省	荆州市	松滋市
62	松滋市陈店镇陈店小学马峪河校区	湖北省	荆州市	松滋市
63	松滋市陈店镇中心幼儿园	湖北省	荆州市	松滋市
64	松滋市南海镇南海初级中学	湖北省	荆州市	松滋市
65	松滋市南海镇新垱学校	湖北省	荆州市	松滋市
66	松滋市南海镇南海小学	湖北省	荆州市	松滋市
67	松滋市南海镇东湖小学	湖北省	荆州市	松滋市
68	南海镇小南海幼儿园	湖北省	荆州市	松滋市
69	南海镇启智幼儿园	湖北省	荆州市	松滋市
70	南海镇东湖幼儿园	湖北省	荆州市	松滋市
71	南海镇新垱幼儿园	湖北省	荆州市	松滋市
72	南海镇文家铺幼儿园	湖北省	荆州市	松滋市
73	南海镇中心幼儿园	湖北省	荆州市	松滋市
74	松滋市卸甲坪乡镇泰民族学校	湖北省	荆州市	松滋市
75	松滋市卸甲坪乡土家族乡明德小学	湖北省	荆州市	松滋市
76	松滋市卸甲坪乡土家族乡曲尺河小学	湖北省	荆州市	松滋市
77	松滋市卸甲坪乡土家族乡利民幼儿园	湖北省	荆州市	松滋市
78	松滋市卸甲坪乡土家族乡中心幼儿园	湖北省	荆州市	松滋市
\.


--
-- Data for Name: school_stage; Type: TABLE DATA; Schema: public; Owner: gab
--

COPY public.school_stage (school_id, stage_id) FROM stdin;
2	3
10	2
10	4
3	3
4	1
5	1
6	1
7	1
8	1
9	1
11	2
11	4
11	5
12	2
13	2
14	2
15	1
16	1
17	1
18	1
19	1
20	1
21	1
22	1
23	1
24	1
25	1
26	1
27	3
28	2
29	2
30	2
31	1
32	1
33	1
34	3
35	3
36	2
37	2
38	2
39	2
40	2
41	2
42	2
43	1
44	1
45	1
46	1
47	1
48	1
49	1
50	3
51	2
52	2
53	2
54	1
55	1
56	1
57	1
58	3
59	2
60	2
61	2
62	2
63	1
64	3
65	2
66	2
67	2
68	1
69	1
70	1
71	1
72	1
73	1
74	2
74	3
75	2
76	2
77	1
78	1
\.


--
-- Data for Name: stage; Type: TABLE DATA; Schema: public; Owner: gab
--

COPY public.stage (id, name, grades) FROM stdin;
1	学前	小班,中班,大班
2	小学	一年级,二年级,三年级,四年级,五年级,六年级
3	初中	七年级,八年级,九年级
4	初中2	初一,初二,初三
5	高中	高一,高二,高三
\.


--
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: gab
--

COPY public."user" (id, username, roles, password, name, phone, idnum) FROM stdin;
\.


--
-- Name: insured_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gab
--

SELECT pg_catalog.setval('public.insured_id_seq', 1, false);


--
-- Name: messenger_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gab
--

SELECT pg_catalog.setval('public.messenger_messages_id_seq', 1, false);


--
-- Name: order_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gab
--

SELECT pg_catalog.setval('public.order_id_seq', 1, false);


--
-- Name: product_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gab
--

SELECT pg_catalog.setval('public.product_id_seq', 1, true);


--
-- Name: school_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gab
--

SELECT pg_catalog.setval('public.school_id_seq', 78, true);


--
-- Name: stage_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gab
--

SELECT pg_catalog.setval('public.stage_id_seq', 5, true);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gab
--

SELECT pg_catalog.setval('public.user_id_seq', 1, false);


--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: insured insured_pkey; Type: CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.insured
    ADD CONSTRAINT insured_pkey PRIMARY KEY (id);


--
-- Name: messenger_messages messenger_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.messenger_messages
    ADD CONSTRAINT messenger_messages_pkey PRIMARY KEY (id);


--
-- Name: order order_pkey; Type: CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public."order"
    ADD CONSTRAINT order_pkey PRIMARY KEY (id);


--
-- Name: product product_pkey; Type: CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.product
    ADD CONSTRAINT product_pkey PRIMARY KEY (id);


--
-- Name: school school_pkey; Type: CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.school
    ADD CONSTRAINT school_pkey PRIMARY KEY (id);


--
-- Name: school_stage school_stage_pkey; Type: CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.school_stage
    ADD CONSTRAINT school_stage_pkey PRIMARY KEY (school_id, stage_id);


--
-- Name: stage stage_pkey; Type: CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.stage
    ADD CONSTRAINT stage_pkey PRIMARY KEY (id);


--
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: idx_1c8625732298d193; Type: INDEX; Schema: public; Owner: gab
--

CREATE INDEX idx_1c8625732298d193 ON public.school_stage USING btree (stage_id);


--
-- Name: idx_1c862573c32a47ee; Type: INDEX; Schema: public; Owner: gab
--

CREATE INDEX idx_1c862573c32a47ee ON public.school_stage USING btree (school_id);


--
-- Name: idx_560ff44ec32a47ee; Type: INDEX; Schema: public; Owner: gab
--

CREATE INDEX idx_560ff44ec32a47ee ON public.insured USING btree (school_id);


--
-- Name: idx_75ea56e016ba31db; Type: INDEX; Schema: public; Owner: gab
--

CREATE INDEX idx_75ea56e016ba31db ON public.messenger_messages USING btree (delivered_at);


--
-- Name: idx_75ea56e0e3bd61ce; Type: INDEX; Schema: public; Owner: gab
--

CREATE INDEX idx_75ea56e0e3bd61ce ON public.messenger_messages USING btree (available_at);


--
-- Name: idx_75ea56e0fb7336f0; Type: INDEX; Schema: public; Owner: gab
--

CREATE INDEX idx_75ea56e0fb7336f0 ON public.messenger_messages USING btree (queue_name);


--
-- Name: idx_f52993984584665a; Type: INDEX; Schema: public; Owner: gab
--

CREATE INDEX idx_f52993984584665a ON public."order" USING btree (product_id);


--
-- Name: idx_f529939897139001; Type: INDEX; Schema: public; Owner: gab
--

CREATE INDEX idx_f529939897139001 ON public."order" USING btree (applicant_id);


--
-- Name: idx_f5299398fc2a5c84; Type: INDEX; Schema: public; Owner: gab
--

CREATE INDEX idx_f5299398fc2a5c84 ON public."order" USING btree (insured_id);


--
-- Name: uniq_identifier_username; Type: INDEX; Schema: public; Owner: gab
--

CREATE UNIQUE INDEX uniq_identifier_username ON public."user" USING btree (username);


--
-- Name: messenger_messages notify_trigger; Type: TRIGGER; Schema: public; Owner: gab
--

CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON public.messenger_messages FOR EACH ROW EXECUTE FUNCTION public.notify_messenger_messages();


--
-- Name: school_stage fk_1c8625732298d193; Type: FK CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.school_stage
    ADD CONSTRAINT fk_1c8625732298d193 FOREIGN KEY (stage_id) REFERENCES public.stage(id) ON DELETE CASCADE;


--
-- Name: school_stage fk_1c862573c32a47ee; Type: FK CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.school_stage
    ADD CONSTRAINT fk_1c862573c32a47ee FOREIGN KEY (school_id) REFERENCES public.school(id) ON DELETE CASCADE;


--
-- Name: insured fk_560ff44ec32a47ee; Type: FK CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public.insured
    ADD CONSTRAINT fk_560ff44ec32a47ee FOREIGN KEY (school_id) REFERENCES public.school(id);


--
-- Name: order fk_f52993984584665a; Type: FK CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public."order"
    ADD CONSTRAINT fk_f52993984584665a FOREIGN KEY (product_id) REFERENCES public.product(id);


--
-- Name: order fk_f529939897139001; Type: FK CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public."order"
    ADD CONSTRAINT fk_f529939897139001 FOREIGN KEY (applicant_id) REFERENCES public."user"(id);


--
-- Name: order fk_f5299398fc2a5c84; Type: FK CONSTRAINT; Schema: public; Owner: gab
--

ALTER TABLE ONLY public."order"
    ADD CONSTRAINT fk_f5299398fc2a5c84 FOREIGN KEY (insured_id) REFERENCES public.insured(id);


--
-- PostgreSQL database dump complete
--

