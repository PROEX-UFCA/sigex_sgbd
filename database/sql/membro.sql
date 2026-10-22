WITH projetos_validos AS (

    SELECT
        p.id_projeto
    FROM projetos.projeto p
    WHERE
        p.id_tipo_situacao_projeto IN (105, 103)
        AND p.ano > 2020
        AND p.titulo !~* '(^|[^[:alnum:]_])test[[:alnum:]_]*([^[:alnum:]_]|$)'
        AND p.titulo !~* '(^|[^[:alnum:]_])EXTENSÃO_TESTE[[:alnum:]_]*([^[:alnum:]_]|$)'
        AND UPPER(TRIM(p.titulo)) !~ '^A+$'
        AND UPPER(TRIM(p.titulo)) !~ '^J+$'
        AND p.id_tipo_projeto = 3
        AND p.ativo = TRUE
),


membros_nao_discentes AS (
    SELECT DISTINCT ON (p.id_projeto, mp.id_pessoa)
        p.id_projeto,
        p2.id_pessoa,
        p2.nome_ascii,
        p2.sexo,
        p2.email,
        mp.data_inicio::date AS data_inicio,
        mp.data_fim::date AS data_fim,
        CASE
            WHEN mp.data_fim::date < CURRENT_DATE
                THEN 'FINALIZADO'
            ELSE 'ATIVO'
        END AS status,
        cm.descricao AS tipo_membro,
        fm.descricao AS categoria_membro
    FROM projetos.membro_projeto mp
    INNER JOIN projetos_validos pv
        ON pv.id_projeto = mp.id_projeto
    INNER JOIN projetos.projeto p
        ON p.id_projeto = mp.id_projeto
    LEFT JOIN projetos.categoria_membro cm
        ON cm.id_categoria_membro = mp.id_categoria_membro
    LEFT JOIN projetos.funcao_membro fm
        ON fm.id_funcao_membro = mp.id_funcao_membro
    LEFT JOIN comum.pessoa p2
        ON p2.id_pessoa = mp.id_pessoa
    WHERE
        mp.id_categoria_membro <> 2
    ORDER BY
        p.id_projeto,
        mp.id_pessoa,
        mp.data_fim DESC NULLS LAST
),


membros_discentes AS (
    SELECT DISTINCT ON (p.id_projeto, mp.id_pessoa)
        p.id_projeto,
        p2.id_pessoa,
        p2.nome_ascii,
        p2.sexo,
        p2.email,
        mp.data_inicio::date AS data_inicio,
        mp.data_fim::date AS data_fim,
        CASE
            WHEN mp.data_fim::date < CURRENT_DATE
                THEN 'FINALIZADO'
            ELSE 'ATIVO'
        END AS status,
        cm.descricao AS tipo_membro,
        fm.descricao AS categoria_membro
    FROM projetos.membro_projeto mp
    INNER JOIN projetos_validos pv
        ON pv.id_projeto = mp.id_projeto
    INNER JOIN projetos.projeto p
        ON p.id_projeto = mp.id_projeto
    LEFT JOIN projetos.categoria_membro cm
        ON cm.id_categoria_membro = mp.id_categoria_membro
    LEFT JOIN projetos.funcao_membro fm
        ON fm.id_funcao_membro = mp.id_funcao_membro
    LEFT JOIN comum.pessoa p2
        ON p2.id_pessoa = mp.id_pessoa
    WHERE
        mp.id_categoria_membro = 2
        AND fm.id_funcao_membro NOT IN (105, 106, 99)
    ORDER BY
        p.id_projeto,
        mp.id_pessoa,
        mp.data_fim DESC NULLS LAST
),


bolsistas_voluntarios AS (
    SELECT DISTINCT ON (p.id_projeto, p2.id_pessoa)
        p.id_projeto,
        p2.id_pessoa,
        p2.nome_ascii,
        p2.sexo,
        p2.email,
        db.data_inicio::date AS data_inicio,
        LEAST(
            db.data_fim::date,
            de.data_fim::date
        ) AS data_fim,
        tsde.descricao AS status,
        'DISCENTE' AS tipo_membro,
        tvd.descricao AS categoria_membro
    FROM extensao.discente_extensao de
    LEFT JOIN comum.discente_bolsista db
        ON db.id_discente_bolsista = de.id_discente_extensao
    LEFT JOIN extensao.atividade a
        ON a.id_atividade = de.id_atividade
    INNER JOIN projetos_validos pv
        ON pv.id_projeto = a.id_projeto
    INNER JOIN projetos.projeto p
        ON p.id_projeto = a.id_projeto
    LEFT JOIN projetos.tipo_vinculo_discente tvd
        ON tvd.id_tipo_vinculo_discente = de.id_tipo_vinculo_discente
    LEFT JOIN extensao.tipo_situacao_discente_extensao tsde
        ON tsde.id_tipo_situacao_discente_extensao =
           de.id_situacao_discente_extensao
    LEFT JOIN public.discente d
        ON d.id_discente = db.id_discente
    LEFT JOIN comum.pessoa p2
        ON p2.id_pessoa = d.id_pessoa
    WHERE
        tsde.id_tipo_situacao_discente_extensao IN (4, 5)
    ORDER BY
        p.id_projeto,
        p2.id_pessoa,
        db.data_fim DESC NULLS LAST
)


SELECT *
FROM membros_nao_discentes

UNION ALL

SELECT *
FROM membros_discentes

UNION ALL

SELECT *
FROM bolsistas_voluntarios

ORDER BY
    id_projeto,
    nome_ascii;