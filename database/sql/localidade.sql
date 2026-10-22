SELECT p.id_projeto, lr.descricao , m.nome, uf.descricao AS estado, lr.classificacao  
FROM projetos.projeto p
LEFT JOIN projetos.tipo_situacao_projeto tsp
    ON p.id_tipo_situacao_projeto = tsp.id_tipo_situacao_projeto
LEFT JOIN extensao.atividade a
    ON a.id_projeto = p.id_projeto
LEFT JOIN extensao.local_realizacao lr 
	ON lr.id_atividade = a.id_atividade 
LEFT JOIN comum.municipio m 
	ON m.id_municipio = lr.id_municipio 
LEFT JOIN comum.unidade_federativa uf 
	ON uf.id_unidade_federativa = m.id_unidade_federativa 
WHERE
    p.id_tipo_situacao_projeto IN (105, 103)
    AND p.ano > 2020
    AND p.titulo !~* '(^|[^[:alnum:]_])test[[:alnum:]_]*([^[:alnum:]_]|$)'
    AND p.titulo !~* '(^|[^[:alnum:]_])EXTENSÃO_TESTE[[:alnum:]_]*([^[:alnum:]_]|$)'
    AND UPPER(TRIM(p.titulo)) !~ '^A+$'
    AND UPPER(TRIM(p.titulo)) !~ '^J+$'
    AND p.id_tipo_projeto = 3
    AND p.ativo = TRUE