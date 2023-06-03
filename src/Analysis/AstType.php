<?php

namespace BaptisteContreras\TaintChecker\Analysis;

enum AstType: string
{
    case EXPR_ASSIGNATION = 'Expr_Assign';
    case STMT_CLASS_METHOD = 'Stmt_ClassMethod';
    case STMT_USE = 'Stmt_Use';
}
