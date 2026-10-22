SELECT DISTINCT
	p.id_projeto,
	p.ano,
	UPPER(
		    TRIM(
		        REGEXP_REPLACE(p.titulo, '[\r\n]+', ' ', 'g')
		    )
		) AS titulo,
    CASE
	    WHEN p.id_edital IN (33320637, 33015996, 33699820, 34589015, 34035954, 35086228, 35869497) THEN 'PROPE'
	    WHEN p.id_edital IN (35869522, 34595643, 34095489, 35087210) THEN 'UFCA ITINERANTE'
	    WHEN p.id_edital IN (33015991, 33320584, 33699823, 33320635, 34589017, 34035905, 34035950, 35086231, 35086233, 34588502, 35869513) THEN 'AMPLA CONCORRÊNCIA'
	    ELSE 'FLUXO CONTÍNUO'
	END AS edital,
	a.bolsas_solicitadas,
    a.bolsas_concedidas,
	CASE
	  WHEN a.bolsas_concedidas > 0 THEN 'SIM'
	  ELSE 'NÃO'
	END AS financiamento_interno,
	CASE
	  WHEN p.financiamento_externo = TRUE THEN 'SIM'
	  ELSE 'NÃO'
	END AS financiamento_externo,
	COALESCE(NULLIF(UPPER(TRIM(tsp.descricao)), ''), 'SEM STATUS') AS situacao,
    p.data_cadastro::date AS data_cadastro,
	p.data_inicio::date AS data_inicio,
    p.data_fim::date AS data_fim,
    p.data_atualizacao::date AS data_atualizacao,
    tae.descricao AS tipo_acao,
    u.sigla,
    t.descricao AS area_tematica,
    p.resumo,
    REPLACE(p.palavras_chave, ',', ';') AS palavras_chave,
    STRING_AGG(
    DISTINCT (odsp.id_objetivo_desenvolvimento_sustentavel + 1)::text,
    '; '
    ORDER BY (odsp.id_objetivo_desenvolvimento_sustentavel + 1)::text
    ) AS ods,
    CASE
	    WHEN tsp.id_tipo_situacao_projeto IN (105, 103) THEN 'APROVAÇÃO'
	    WHEN tsp.id_tipo_situacao_projeto IN (316, 315, 309, 308, 104) THEN 'SUBMISSÃO'
	END AS contexto
FROM projetos.projeto p
LEFT JOIN projetos.tipo_situacao_projeto tsp
    ON p.id_tipo_situacao_projeto = tsp.id_tipo_situacao_projeto
LEFT JOIN projetos.objetivo_desenvolvimento_sustentavel_projeto odsp
    ON p.id_projeto = odsp.id_projeto
LEFT JOIN comum.unidade u
    ON p.id_unidade = u.id_unidade
LEFT JOIN comum.area_conhecimento_cnpq acc
    ON p.id_area_conhecimento_cnpq = acc.id_area_conhecimento_cnpq
LEFT JOIN extensao.atividade a
    ON a.id_projeto = p.id_projeto
LEFT JOIN extensao.area_tematica t
        ON t.id_area_tematica = a.id_area_tematica_principal
LEFT JOIN extensao.tipo_atividade_extensao tae
    ON tae.id_tipo_atividade_extensao = a.id_tipo_atividade_extensao
WHERE
	tsp.id_tipo_situacao_projeto IN (105, 103)
	AND p.ano > 2020
	AND p.titulo !~* '(^|[^[:alnum:]_])test[[:alnum:]_]*([^[:alnum:]_]|$)'
	AND p.titulo !~* '(^|[^[:alnum:]_])EXTENSÃO_TESTE[[:alnum:]_]*([^[:alnum:]_]|$)'
	AND UPPER(TRIM(p.titulo)) !~ '^A+$'
	AND UPPER(TRIM(p.titulo)) !~ '^J+$'
	AND p.id_tipo_projeto = 3
	AND p.ativo = TRUE
GROUP BY 
	p.id_projeto,
	a.bolsas_solicitadas,
    a.bolsas_concedidas,
    tsp.id_tipo_situacao_projeto,
    tae.descricao,
    u.sigla,
    t.descricao