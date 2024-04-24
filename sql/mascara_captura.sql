PGDMP  4        
            |            mascaras_capturas    16.2    16.2 V    J           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                      false            K           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                      false            L           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                      false            M           1262    32917    mascaras_capturas    DATABASE     �   CREATE DATABASE mascaras_capturas WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Spanish_Mexico.1252';
 !   DROP DATABASE mascaras_capturas;
                postgres    false                        2615    2200    public    SCHEMA        CREATE SCHEMA public;
    DROP SCHEMA public;
                pg_database_owner    false            N           0    0    SCHEMA public    COMMENT     6   COMMENT ON SCHEMA public IS 'standard public schema';
                   pg_database_owner    false    4            �            1255    41304 a   sp_insertar_datos_campo(character varying, character varying, integer, integer, integer, integer)    FUNCTION     "  CREATE FUNCTION public.sp_insertar_datos_campo(p_titulo_campo character varying, p_nombre_campo character varying, p_id_tags_campos integer, p_id_css_columnas integer, p_id_tipo_formulario integer, p_id_nombre_catalogo_datos integer) RETURNS TABLE(id_campo integer, titulo_campo character varying, texto_tag character varying, clase_css character varying, texto_columnas character varying, nombre_catalogo character varying)
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_id_datos_campos INT;
BEGIN
    -- Insertar el nuevo registro y obtener el ID resultante
    INSERT INTO datoscampos (titulo_campo, nombre_campo, id_tags_campos, id_css_columnas, id_tipo_formulario, id_nombre_catalogo_datos)
    VALUES (p_titulo_campo, p_nombre_campo, p_id_tags_campos, p_id_css_columnas, p_id_tipo_formulario, p_id_nombre_catalogo_datos)
    RETURNING id_datos_campos INTO v_id_datos_campos;

    -- Realizar la consulta utilizando el ID obtenido
    RETURN QUERY
    SELECT datoscampos.id_datos_campos AS id_campo,
           datoscampos.titulo_campo,
           tagscampos.texto AS texto_tag,
           csscolumnas.cssclass AS clase_css,
           csscolumnas.texto AS texto_columnas,
           nombrecatalogodatos.nombre_catalogo as texto_catalogo
    FROM datoscampos
    LEFT JOIN (
        SELECT *
        FROM tagscampos
        WHERE p_id_tags_campos IS NOT NULL
    ) AS tagscampos ON datoscampos.id_tags_campos = tagscampos.id_tags_campos
    LEFT JOIN (
        SELECT *
        FROM csscolumnas
        WHERE p_id_css_columnas IS NOT NULL
    ) AS csscolumnas ON datoscampos.id_css_columnas = csscolumnas.id_css_columnas
    LEFT JOIN (
        SELECT *
        FROM nombrecatalogodatos
        WHERE p_id_nombre_catalogo_datos IS NOT NULL
    ) AS nombrecatalogodatos ON datoscampos.id_nombre_catalogo_datos = nombrecatalogodatos.id_nombre_catalogo_datos
    WHERE datoscampos.id_tipo_formulario = p_id_tipo_formulario AND datoscampos.id_datos_campos = v_id_datos_campos;

    -- Devolver los resultados de la consulta
    RETURN;
END;
$$;
 �   DROP FUNCTION public.sp_insertar_datos_campo(p_titulo_campo character varying, p_nombre_campo character varying, p_id_tags_campos integer, p_id_css_columnas integer, p_id_tipo_formulario integer, p_id_nombre_catalogo_datos integer);
       public          postgres    false    4            �            1255    41232    sp_mostrarformulario(integer)    FUNCTION     �	  CREATE FUNCTION public.sp_mostrarformulario(v_id_tipo_formulario integer) RETURNS TABLE(htmltag text)
    LANGUAGE plpgsql
    AS $$
DECLARE
    optionHtml TEXT := '';
    rowRecord RECORD;
   
		begin
			FOR rowRecord IN 
				select cd.id_catalogo_datos ,cd.nombre_datos from  datoscampos dc  
				left join catalogodatos cd on dc.id_nombre_catalogo_datos = cd.id_nombre_catalogo_datos 
				left join nombrecatalogodatos ncd on ncd.id_nombre_catalogo_datos = dc.id_nombre_catalogo_datos  
				where dc.id_tipo_formulario = v_id_tipo_formulario  and cd.id_catalogo_datos is not null   
			LOOP
        		optionHtml := optionHtml || format('<option value="%s">%s</option>', rowRecord.nombre_datos, rowRecord.nombre_datos);
    		END LOOP;
			return query
					SELECT 
						case
							when tc.tag = 'input' 
								then concat(
									'<div class="', cc.cssclass, ' mb-2">'
									'<label for="', dc.nombre_campo, '" class="col-12 text-center">', dc.titulo_campo, '</labe>'
									'<', tc.tag, ' class="form-control" type="', tc.tipoinput ,'" ', 'name="', dc.nombre_campo, '" id="', dc.nombre_campo, '" id-cust="', dc.id_datos_campos, '">'
									'</div>'
								)
							when tc.tag = 'textarea' 
								then concat(
									'<div class="', cc.cssclass, ' mb-2">'
									'<label for="', dc.nombre_campo, '" class="col-12 text-center">', dc.titulo_campo, '</labe>'
									'<', tc.tag, ' class="form-control" name="', dc.nombre_campo, '" id="', dc.nombre_campo, '" id-cust="', dc.id_datos_campos, '">','</', tc.tag, '>'
									'</div>'
								)
					        WHEN tc.tag = 'select' THEN 
					                concat(
					                    '<div class="', cc.cssclass, ' mb-2">',
					                    '<label for="', dc.nombre_campo, '" class="col-12 text-center">', dc.titulo_campo, '</label>',
					                    '<', tc.tag, ' class="form-control" name="', dc.nombre_campo, '" id="', dc.nombre_campo, '" id-cust="', dc.id_datos_campos, '">',
					                    optionHtml, -- Agregar las opciones generadas dinámicamente aquí
					                    '</', tc.tag, '>',
					                    '</div>'
					                )
						end as htmlTag
					FROM datoscampos dc
					LEFT JOIN tagscampos tc ON dc.id_tags_campos = tc.id_tags_campos
					LEFT JOIN csscolumnas cc ON dc.id_css_columnas = cc.id_css_columnas
					WHERE dc.id_tipo_formulario = v_id_tipo_formulario AND dc.id_tipo_formulario = v_id_tipo_formulario
					order by dc.id_datos_campos asc;
		end
	$$;
 I   DROP FUNCTION public.sp_mostrarformulario(v_id_tipo_formulario integer);
       public          postgres    false    4            �            1259    41285    catalogodatos    TABLE     �   CREATE TABLE public.catalogodatos (
    id_catalogo_datos integer NOT NULL,
    nombre_datos character varying NOT NULL,
    id_nombre_catalogo_datos integer NOT NULL
);
 !   DROP TABLE public.catalogodatos;
       public         heap    postgres    false    4            �            1259    41284 #   catalogodatos_id_catalogo_datos_seq    SEQUENCE     �   CREATE SEQUENCE public.catalogodatos_id_catalogo_datos_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 :   DROP SEQUENCE public.catalogodatos_id_catalogo_datos_seq;
       public          postgres    false    230    4            O           0    0 #   catalogodatos_id_catalogo_datos_seq    SEQUENCE OWNED BY     k   ALTER SEQUENCE public.catalogodatos_id_catalogo_datos_seq OWNED BY public.catalogodatos.id_catalogo_datos;
          public          postgres    false    229            �            1259    32990    csscolumnas    TABLE     �   CREATE TABLE public.csscolumnas (
    id_css_columnas integer NOT NULL,
    texto character varying,
    cssclass character varying
);
    DROP TABLE public.csscolumnas;
       public         heap    postgres    false    4            �            1259    32989    csscolumnas_id_css_columnas_seq    SEQUENCE     �   CREATE SEQUENCE public.csscolumnas_id_css_columnas_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 6   DROP SEQUENCE public.csscolumnas_id_css_columnas_seq;
       public          postgres    false    224    4            P           0    0    csscolumnas_id_css_columnas_seq    SEQUENCE OWNED BY     c   ALTER SEQUENCE public.csscolumnas_id_css_columnas_seq OWNED BY public.csscolumnas.id_css_columnas;
          public          postgres    false    223            �            1259    33008    datoscampos    TABLE       CREATE TABLE public.datoscampos (
    id_datos_campos integer NOT NULL,
    titulo_campo character varying,
    nombre_campo character varying,
    id_tags_campos integer,
    id_css_columnas integer,
    id_tipo_formulario integer,
    id_nombre_catalogo_datos integer
);
    DROP TABLE public.datoscampos;
       public         heap    postgres    false    4            �            1259    33007    datoscampos_id_datos_campos_seq    SEQUENCE     �   CREATE SEQUENCE public.datoscampos_id_datos_campos_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 6   DROP SEQUENCE public.datoscampos_id_datos_campos_seq;
       public          postgres    false    4    226            Q           0    0    datoscampos_id_datos_campos_seq    SEQUENCE OWNED BY     c   ALTER SEQUENCE public.datoscampos_id_datos_campos_seq OWNED BY public.datoscampos.id_datos_campos;
          public          postgres    false    225            �            1259    32920    datosformulario    TABLE     @  CREATE TABLE public.datosformulario (
    id_datos_formulario integer NOT NULL,
    nombre_solicitante text,
    cargo text,
    correo_electronico text,
    telefono_celular text,
    telefono_oficina_ext text,
    ubicacion text,
    correo_institucional text,
    ruta_archivo text,
    id_tipo_formulario integer
);
 #   DROP TABLE public.datosformulario;
       public         heap    postgres    false    4            �            1259    32925 '   datosformulario_id_datos_formulario_seq    SEQUENCE     �   CREATE SEQUENCE public.datosformulario_id_datos_formulario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 >   DROP SEQUENCE public.datosformulario_id_datos_formulario_seq;
       public          postgres    false    215    4            R           0    0 '   datosformulario_id_datos_formulario_seq    SEQUENCE OWNED BY     s   ALTER SEQUENCE public.datosformulario_id_datos_formulario_seq OWNED BY public.datosformulario.id_datos_formulario;
          public          postgres    false    216            �            1259    41308    detalle    TABLE     �   CREATE TABLE public.detalle (
    id integer NOT NULL,
    id_tipo_formulario integer,
    txt_asunto text,
    txt_fecha text,
    txt_titulo text,
    asunto text,
    fecha_vencimiento text
);
    DROP TABLE public.detalle;
       public         heap    postgres    false    4            �            1259    41307    detalle_id_seq    SEQUENCE     �   CREATE SEQUENCE public.detalle_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 %   DROP SEQUENCE public.detalle_id_seq;
       public          postgres    false    232    4            S           0    0    detalle_id_seq    SEQUENCE OWNED BY     A   ALTER SEQUENCE public.detalle_id_seq OWNED BY public.detalle.id;
          public          postgres    false    231            �            1259    41322    ejemplo    TABLE     >  CREATE TABLE public.ejemplo (
    id integer NOT NULL,
    id_tipo_formulario integer,
    txt_curp text,
    txt_nombre text,
    txt_apellidopaterno text,
    txt_apellidomaterno text,
    txt_correo text,
    txt_edad integer,
    txt_firmas text,
    txt_firma text,
    asunto text,
    fecha_vencimiento text
);
    DROP TABLE public.ejemplo;
       public         heap    postgres    false    4            �            1259    41321    ejemplo_id_seq    SEQUENCE     �   CREATE SEQUENCE public.ejemplo_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 %   DROP SEQUENCE public.ejemplo_id_seq;
       public          postgres    false    234    4            T           0    0    ejemplo_id_seq    SEQUENCE OWNED BY     A   ALTER SEQUENCE public.ejemplo_id_seq OWNED BY public.ejemplo.id;
          public          postgres    false    233            �            1259    41248    nombrecatalogodatos    TABLE     �   CREATE TABLE public.nombrecatalogodatos (
    id_nombre_catalogo_datos integer NOT NULL,
    nombre_catalogo character varying NOT NULL
);
 '   DROP TABLE public.nombrecatalogodatos;
       public         heap    postgres    false    4            �            1259    41247 0   nombrecatalogodatos_id_nombre_catalogo_datos_seq    SEQUENCE     �   CREATE SEQUENCE public.nombrecatalogodatos_id_nombre_catalogo_datos_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 G   DROP SEQUENCE public.nombrecatalogodatos_id_nombre_catalogo_datos_seq;
       public          postgres    false    228    4            U           0    0 0   nombrecatalogodatos_id_nombre_catalogo_datos_seq    SEQUENCE OWNED BY     �   ALTER SEQUENCE public.nombrecatalogodatos_id_nombre_catalogo_datos_seq OWNED BY public.nombrecatalogodatos.id_nombre_catalogo_datos;
          public          postgres    false    227            �            1259    32981 
   tagscampos    TABLE     �   CREATE TABLE public.tagscampos (
    id_tags_campos integer NOT NULL,
    texto character varying,
    tag character varying,
    tipoinput character varying
);
    DROP TABLE public.tagscampos;
       public         heap    postgres    false    4            �            1259    32980    tagscampos_id_tags_campos_seq    SEQUENCE     �   CREATE SEQUENCE public.tagscampos_id_tags_campos_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 4   DROP SEQUENCE public.tagscampos_id_tags_campos_seq;
       public          postgres    false    4    222            V           0    0    tagscampos_id_tags_campos_seq    SEQUENCE OWNED BY     _   ALTER SEQUENCE public.tagscampos_id_tags_campos_seq OWNED BY public.tagscampos.id_tags_campos;
          public          postgres    false    221            �            1259    32926    tagsformulario    TABLE     �   CREATE TABLE public.tagsformulario (
    id_tags_formulario integer NOT NULL,
    texto text,
    tag text,
    tipoinput text,
    cssclass text,
    idtag text
);
 "   DROP TABLE public.tagsformulario;
       public         heap    postgres    false    4            �            1259    32931 %   tagsformulario_id_tags_formulario_seq    SEQUENCE     �   CREATE SEQUENCE public.tagsformulario_id_tags_formulario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 <   DROP SEQUENCE public.tagsformulario_id_tags_formulario_seq;
       public          postgres    false    217    4            W           0    0 %   tagsformulario_id_tags_formulario_seq    SEQUENCE OWNED BY     o   ALTER SEQUENCE public.tagsformulario_id_tags_formulario_seq OWNED BY public.tagsformulario.id_tags_formulario;
          public          postgres    false    218            �            1259    32932    tipoformulario    TABLE     �   CREATE TABLE public.tipoformulario (
    id_tipo_formulario integer NOT NULL,
    nombre_formulario text NOT NULL,
    texto text
);
 "   DROP TABLE public.tipoformulario;
       public         heap    postgres    false    4            �            1259    32937 %   tipoformulario_id_tipo_formulario_seq    SEQUENCE     �   CREATE SEQUENCE public.tipoformulario_id_tipo_formulario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 <   DROP SEQUENCE public.tipoformulario_id_tipo_formulario_seq;
       public          postgres    false    4    219            X           0    0 %   tipoformulario_id_tipo_formulario_seq    SEQUENCE OWNED BY     o   ALTER SEQUENCE public.tipoformulario_id_tipo_formulario_seq OWNED BY public.tipoformulario.id_tipo_formulario;
          public          postgres    false    220            �           2604    41288    catalogodatos id_catalogo_datos    DEFAULT     �   ALTER TABLE ONLY public.catalogodatos ALTER COLUMN id_catalogo_datos SET DEFAULT nextval('public.catalogodatos_id_catalogo_datos_seq'::regclass);
 N   ALTER TABLE public.catalogodatos ALTER COLUMN id_catalogo_datos DROP DEFAULT;
       public          postgres    false    230    229    230            �           2604    32993    csscolumnas id_css_columnas    DEFAULT     �   ALTER TABLE ONLY public.csscolumnas ALTER COLUMN id_css_columnas SET DEFAULT nextval('public.csscolumnas_id_css_columnas_seq'::regclass);
 J   ALTER TABLE public.csscolumnas ALTER COLUMN id_css_columnas DROP DEFAULT;
       public          postgres    false    224    223    224            �           2604    33011    datoscampos id_datos_campos    DEFAULT     �   ALTER TABLE ONLY public.datoscampos ALTER COLUMN id_datos_campos SET DEFAULT nextval('public.datoscampos_id_datos_campos_seq'::regclass);
 J   ALTER TABLE public.datoscampos ALTER COLUMN id_datos_campos DROP DEFAULT;
       public          postgres    false    226    225    226                       2604    32938 #   datosformulario id_datos_formulario    DEFAULT     �   ALTER TABLE ONLY public.datosformulario ALTER COLUMN id_datos_formulario SET DEFAULT nextval('public.datosformulario_id_datos_formulario_seq'::regclass);
 R   ALTER TABLE public.datosformulario ALTER COLUMN id_datos_formulario DROP DEFAULT;
       public          postgres    false    216    215            �           2604    41311 
   detalle id    DEFAULT     h   ALTER TABLE ONLY public.detalle ALTER COLUMN id SET DEFAULT nextval('public.detalle_id_seq'::regclass);
 9   ALTER TABLE public.detalle ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    232    231    232            �           2604    41325 
   ejemplo id    DEFAULT     h   ALTER TABLE ONLY public.ejemplo ALTER COLUMN id SET DEFAULT nextval('public.ejemplo_id_seq'::regclass);
 9   ALTER TABLE public.ejemplo ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    234    233    234            �           2604    41251 ,   nombrecatalogodatos id_nombre_catalogo_datos    DEFAULT     �   ALTER TABLE ONLY public.nombrecatalogodatos ALTER COLUMN id_nombre_catalogo_datos SET DEFAULT nextval('public.nombrecatalogodatos_id_nombre_catalogo_datos_seq'::regclass);
 [   ALTER TABLE public.nombrecatalogodatos ALTER COLUMN id_nombre_catalogo_datos DROP DEFAULT;
       public          postgres    false    228    227    228            �           2604    32984    tagscampos id_tags_campos    DEFAULT     �   ALTER TABLE ONLY public.tagscampos ALTER COLUMN id_tags_campos SET DEFAULT nextval('public.tagscampos_id_tags_campos_seq'::regclass);
 H   ALTER TABLE public.tagscampos ALTER COLUMN id_tags_campos DROP DEFAULT;
       public          postgres    false    222    221    222            �           2604    32939 !   tagsformulario id_tags_formulario    DEFAULT     �   ALTER TABLE ONLY public.tagsformulario ALTER COLUMN id_tags_formulario SET DEFAULT nextval('public.tagsformulario_id_tags_formulario_seq'::regclass);
 P   ALTER TABLE public.tagsformulario ALTER COLUMN id_tags_formulario DROP DEFAULT;
       public          postgres    false    218    217            �           2604    32940 !   tipoformulario id_tipo_formulario    DEFAULT     �   ALTER TABLE ONLY public.tipoformulario ALTER COLUMN id_tipo_formulario SET DEFAULT nextval('public.tipoformulario_id_tipo_formulario_seq'::regclass);
 P   ALTER TABLE public.tipoformulario ALTER COLUMN id_tipo_formulario DROP DEFAULT;
       public          postgres    false    220    219            C          0    41285    catalogodatos 
   TABLE DATA           b   COPY public.catalogodatos (id_catalogo_datos, nombre_datos, id_nombre_catalogo_datos) FROM stdin;
    public          postgres    false    230   �       =          0    32990    csscolumnas 
   TABLE DATA           G   COPY public.csscolumnas (id_css_columnas, texto, cssclass) FROM stdin;
    public          postgres    false    224   �       ?          0    33008    datoscampos 
   TABLE DATA           �   COPY public.datoscampos (id_datos_campos, titulo_campo, nombre_campo, id_tags_campos, id_css_columnas, id_tipo_formulario, id_nombre_catalogo_datos) FROM stdin;
    public          postgres    false    226   ΀       4          0    32920    datosformulario 
   TABLE DATA           �   COPY public.datosformulario (id_datos_formulario, nombre_solicitante, cargo, correo_electronico, telefono_celular, telefono_oficina_ext, ubicacion, correo_institucional, ruta_archivo, id_tipo_formulario) FROM stdin;
    public          postgres    false    215   ��       E          0    41308    detalle 
   TABLE DATA           w   COPY public.detalle (id, id_tipo_formulario, txt_asunto, txt_fecha, txt_titulo, asunto, fecha_vencimiento) FROM stdin;
    public          postgres    false    232   ��       G          0    41322    ejemplo 
   TABLE DATA           �   COPY public.ejemplo (id, id_tipo_formulario, txt_curp, txt_nombre, txt_apellidopaterno, txt_apellidomaterno, txt_correo, txt_edad, txt_firmas, txt_firma, asunto, fecha_vencimiento) FROM stdin;
    public          postgres    false    234   �       A          0    41248    nombrecatalogodatos 
   TABLE DATA           X   COPY public.nombrecatalogodatos (id_nombre_catalogo_datos, nombre_catalogo) FROM stdin;
    public          postgres    false    228   o�       ;          0    32981 
   tagscampos 
   TABLE DATA           K   COPY public.tagscampos (id_tags_campos, texto, tag, tipoinput) FROM stdin;
    public          postgres    false    222   ��       6          0    32926    tagsformulario 
   TABLE DATA           d   COPY public.tagsformulario (id_tags_formulario, texto, tag, tipoinput, cssclass, idtag) FROM stdin;
    public          postgres    false    217   e�       8          0    32932    tipoformulario 
   TABLE DATA           V   COPY public.tipoformulario (id_tipo_formulario, nombre_formulario, texto) FROM stdin;
    public          postgres    false    219   ͅ       Y           0    0 #   catalogodatos_id_catalogo_datos_seq    SEQUENCE SET     Q   SELECT pg_catalog.setval('public.catalogodatos_id_catalogo_datos_seq', 5, true);
          public          postgres    false    229            Z           0    0    csscolumnas_id_css_columnas_seq    SEQUENCE SET     N   SELECT pg_catalog.setval('public.csscolumnas_id_css_columnas_seq', 12, true);
          public          postgres    false    223            [           0    0    datoscampos_id_datos_campos_seq    SEQUENCE SET     O   SELECT pg_catalog.setval('public.datoscampos_id_datos_campos_seq', 209, true);
          public          postgres    false    225            \           0    0 '   datosformulario_id_datos_formulario_seq    SEQUENCE SET     V   SELECT pg_catalog.setval('public.datosformulario_id_datos_formulario_seq', 17, true);
          public          postgres    false    216            ]           0    0    detalle_id_seq    SEQUENCE SET     <   SELECT pg_catalog.setval('public.detalle_id_seq', 1, true);
          public          postgres    false    231            ^           0    0    ejemplo_id_seq    SEQUENCE SET     <   SELECT pg_catalog.setval('public.ejemplo_id_seq', 1, true);
          public          postgres    false    233            _           0    0 0   nombrecatalogodatos_id_nombre_catalogo_datos_seq    SEQUENCE SET     ^   SELECT pg_catalog.setval('public.nombrecatalogodatos_id_nombre_catalogo_datos_seq', 2, true);
          public          postgres    false    227            `           0    0    tagscampos_id_tags_campos_seq    SEQUENCE SET     K   SELECT pg_catalog.setval('public.tagscampos_id_tags_campos_seq', 7, true);
          public          postgres    false    221            a           0    0 %   tagsformulario_id_tags_formulario_seq    SEQUENCE SET     T   SELECT pg_catalog.setval('public.tagsformulario_id_tags_formulario_seq', 19, true);
          public          postgres    false    218            b           0    0 %   tipoformulario_id_tipo_formulario_seq    SEQUENCE SET     S   SELECT pg_catalog.setval('public.tipoformulario_id_tipo_formulario_seq', 6, true);
          public          postgres    false    220            �           2606    41255 $   nombrecatalogodatos catalogodatos_pk 
   CONSTRAINT     x   ALTER TABLE ONLY public.nombrecatalogodatos
    ADD CONSTRAINT catalogodatos_pk PRIMARY KEY (id_nombre_catalogo_datos);
 N   ALTER TABLE ONLY public.nombrecatalogodatos DROP CONSTRAINT catalogodatos_pk;
       public            postgres    false    228            �           2606    32997    csscolumnas csscolumnas_pk 
   CONSTRAINT     e   ALTER TABLE ONLY public.csscolumnas
    ADD CONSTRAINT csscolumnas_pk PRIMARY KEY (id_css_columnas);
 D   ALTER TABLE ONLY public.csscolumnas DROP CONSTRAINT csscolumnas_pk;
       public            postgres    false    224            �           2606    33015    datoscampos datoscampos_pk 
   CONSTRAINT     e   ALTER TABLE ONLY public.datoscampos
    ADD CONSTRAINT datoscampos_pk PRIMARY KEY (id_datos_campos);
 D   ALTER TABLE ONLY public.datoscampos DROP CONSTRAINT datoscampos_pk;
       public            postgres    false    226            �           2606    41313    detalle detalle_pkey 
   CONSTRAINT     R   ALTER TABLE ONLY public.detalle
    ADD CONSTRAINT detalle_pkey PRIMARY KEY (id);
 >   ALTER TABLE ONLY public.detalle DROP CONSTRAINT detalle_pkey;
       public            postgres    false    232            �           2606    41327    ejemplo ejemplo_pkey 
   CONSTRAINT     R   ALTER TABLE ONLY public.ejemplo
    ADD CONSTRAINT ejemplo_pkey PRIMARY KEY (id);
 >   ALTER TABLE ONLY public.ejemplo DROP CONSTRAINT ejemplo_pkey;
       public            postgres    false    234            �           2606    41292    catalogodatos pk_catalogodatos 
   CONSTRAINT     k   ALTER TABLE ONLY public.catalogodatos
    ADD CONSTRAINT pk_catalogodatos PRIMARY KEY (id_catalogo_datos);
 H   ALTER TABLE ONLY public.catalogodatos DROP CONSTRAINT pk_catalogodatos;
       public            postgres    false    230            �           2606    32942 !   tagsformulario pk_datosformulario 
   CONSTRAINT     o   ALTER TABLE ONLY public.tagsformulario
    ADD CONSTRAINT pk_datosformulario PRIMARY KEY (id_tags_formulario);
 K   ALTER TABLE ONLY public.tagsformulario DROP CONSTRAINT pk_datosformulario;
       public            postgres    false    217            �           2606    32944 #   datosformulario pk_datosformularios 
   CONSTRAINT     r   ALTER TABLE ONLY public.datosformulario
    ADD CONSTRAINT pk_datosformularios PRIMARY KEY (id_datos_formulario);
 M   ALTER TABLE ONLY public.datosformulario DROP CONSTRAINT pk_datosformularios;
       public            postgres    false    215            �           2606    32946     tipoformulario pk_tipoformulario 
   CONSTRAINT     n   ALTER TABLE ONLY public.tipoformulario
    ADD CONSTRAINT pk_tipoformulario PRIMARY KEY (id_tipo_formulario);
 J   ALTER TABLE ONLY public.tipoformulario DROP CONSTRAINT pk_tipoformulario;
       public            postgres    false    219            �           2606    32988    tagscampos tagscampos_pk 
   CONSTRAINT     b   ALTER TABLE ONLY public.tagscampos
    ADD CONSTRAINT tagscampos_pk PRIMARY KEY (id_tags_campos);
 B   ALTER TABLE ONLY public.tagscampos DROP CONSTRAINT tagscampos_pk;
       public            postgres    false    222            �           2606    41293 2   catalogodatos catalogodatos_nombrecatalogodatos_fk    FK CONSTRAINT     �   ALTER TABLE ONLY public.catalogodatos
    ADD CONSTRAINT catalogodatos_nombrecatalogodatos_fk FOREIGN KEY (id_nombre_catalogo_datos) REFERENCES public.nombrecatalogodatos(id_nombre_catalogo_datos);
 \   ALTER TABLE ONLY public.catalogodatos DROP CONSTRAINT catalogodatos_nombrecatalogodatos_fk;
       public          postgres    false    4758    228    230            �           2606    33022 &   datoscampos datoscampos_csscolumnas_fk    FK CONSTRAINT     �   ALTER TABLE ONLY public.datoscampos
    ADD CONSTRAINT datoscampos_csscolumnas_fk FOREIGN KEY (id_css_columnas) REFERENCES public.csscolumnas(id_css_columnas);
 P   ALTER TABLE ONLY public.datoscampos DROP CONSTRAINT datoscampos_csscolumnas_fk;
       public          postgres    false    226    224    4754            �           2606    41298 .   datoscampos datoscampos_nombrecatalogodatos_fk    FK CONSTRAINT     �   ALTER TABLE ONLY public.datoscampos
    ADD CONSTRAINT datoscampos_nombrecatalogodatos_fk FOREIGN KEY (id_nombre_catalogo_datos) REFERENCES public.nombrecatalogodatos(id_nombre_catalogo_datos);
 X   ALTER TABLE ONLY public.datoscampos DROP CONSTRAINT datoscampos_nombrecatalogodatos_fk;
       public          postgres    false    228    226    4758            �           2606    33027 %   datoscampos datoscampos_tagscampos_fk    FK CONSTRAINT     �   ALTER TABLE ONLY public.datoscampos
    ADD CONSTRAINT datoscampos_tagscampos_fk FOREIGN KEY (id_tags_campos) REFERENCES public.tagscampos(id_tags_campos);
 O   ALTER TABLE ONLY public.datoscampos DROP CONSTRAINT datoscampos_tagscampos_fk;
       public          postgres    false    226    222    4752            �           2606    33032 )   datoscampos datoscampos_tipoformulario_fk    FK CONSTRAINT     �   ALTER TABLE ONLY public.datoscampos
    ADD CONSTRAINT datoscampos_tipoformulario_fk FOREIGN KEY (id_tipo_formulario) REFERENCES public.tipoformulario(id_tipo_formulario);
 S   ALTER TABLE ONLY public.datoscampos DROP CONSTRAINT datoscampos_tipoformulario_fk;
       public          postgres    false    4750    219    226            �           2606    32947 7   datosformulario datosformulario_id_tipo_formulario_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.datosformulario
    ADD CONSTRAINT datosformulario_id_tipo_formulario_fkey FOREIGN KEY (id_tipo_formulario) REFERENCES public.tipoformulario(id_tipo_formulario);
 a   ALTER TABLE ONLY public.datosformulario DROP CONSTRAINT datosformulario_id_tipo_formulario_fkey;
       public          postgres    false    219    4750    215            �           2606    41314 '   detalle detalle_id_tipo_formulario_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.detalle
    ADD CONSTRAINT detalle_id_tipo_formulario_fkey FOREIGN KEY (id_tipo_formulario) REFERENCES public.tipoformulario(id_tipo_formulario);
 Q   ALTER TABLE ONLY public.detalle DROP CONSTRAINT detalle_id_tipo_formulario_fkey;
       public          postgres    false    219    4750    232            �           2606    41328 '   ejemplo ejemplo_id_tipo_formulario_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.ejemplo
    ADD CONSTRAINT ejemplo_id_tipo_formulario_fkey FOREIGN KEY (id_tipo_formulario) REFERENCES public.tipoformulario(id_tipo_formulario);
 Q   ALTER TABLE ONLY public.ejemplo DROP CONSTRAINT ejemplo_id_tipo_formulario_fkey;
       public          postgres    false    4750    219    234            C   B   x�3�t�,�M�4�2�t�IM.):�9/3$`����\�i�e�\�[���W	�rd �=... K�      =   �   x�U�=
�0�Y>E�vH���)��4��C
��i����I�zL���g��������-���sw>�����=k�I�O���5pC��p#� �dn
��-pG���xޓo�W�{������ >R�a�(<O���M���@<4߭�?
�Հ-L���6�0&̚3~,y<��'�߱6���~ޔR?����      ?   �   x�m���0���S���r4Fnb��L���������Q�˟~m�
y�h�aΝ��P�E�A�mS��B�����|��F" m�]��uV����^�Z��,B*B�[e��_�v�ԙe���V4cpHUu[�����1aC)ѲLc���]$���'�W ` ���'u�H��&?t�c�ӱ XMz����¾�Z���Ia�p�f�=�x�A      4   �   x���;N�0E��Ux��$NDG�B
 �N�K�,9�剥!�a�!#��� ����,�[��5E��8ޤ����
c��"�PO\o
'�Mcr~�x2����[1���,��ZU �T���$�.�Qvt�Z2�f#��g�Y^��{�L*�Dg�"oH��Zm"u���U��7�A�>�>��3%Ə��W'�i)hHq�h� 9�5|��7��	x�TU��cY��`��e)��      E   .   x�3�4�t,.�+��4202�50�54��,)��G�02����� b-�      G   t   x�ƻ	�0��j
-�g}Ҥ�Rd�4��~H�d��Yp��aA4�%d�ڼp�ge���p�K��cRfR�Q����,�7|sŧ����:"�@.G����x���z��w�	!n	�"�      A   ;   x�3�tI-N,*����WHIU(ʯLM.�/�2�t�,�MTp��o��LN����� �=�      ;   �   x�E�=�0Fg�9������.&54R� �"V���Xs1D���{��9���T��ȏ���B�qQَ�@�S`\V�Əc)�qr'�@|
S�͍�.е��a=�{	��e\W�x��-� �=]Lvd,n��_�_|�Y�&�p�e�n��_�P3      6   X  x���AN�0E��S�F8�-,Q�XvlgR,9�L��H�9B/���"�ea�r��GO/ \xoJ�J���8�Wu%��,�u��x�ۑ�KX��3�B��r9���1Q
�b���}\hx�*�(�rC�Y�&���=�7��g?]i�3X�����m3b+�I�G����mv�uW�6/�����QR i�7>��o�_�Aq7((�L	Fd�c�]��8�o��,����Yc/h6}��г�$\��qӞ�(���i���7bq&Ovr�M$�WU��v�)��*W��Ԅ5�f��c1��C��o��'l������2e�u&���^��_o���      8   �   x�=���0E���� �\ Q�a�J(PF���"u/F`1�����+�p6�=Guؘ� �V�%4GJ;;Ij���n�\]��������wQ,��%Z���sZ�b1o��Ih=�I�l�R�5S"� t�=     