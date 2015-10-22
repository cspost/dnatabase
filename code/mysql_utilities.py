"""
This sets up the MySQL database for the DNAtabase project. It inits the tables
for the database using our schema and then downloads and populates the gene
tables with information from Ensembl.
"""

import mysql.connector as sql
import urllib2
import shutil
import subprocess
import os
import gzip


#HOST = 'dnatabase.web.engr.illinois.edu'
HOST = 'localhost'
PORT = '3306'
USER = 'dnatabas_root'
PASS = 'alphabio'
DB = 'dnatabas_cs411'
EDB = "dnatabas_ensembl"

def get_conn(h=HOST, p=PORT, u=USER, ps=PASS, d=DB):
    """Returns a connection to the MySQL database"""
    return sql.connect(host=h, port=p, user=u, password=ps, db=d)

def create_tables():
    """Creates the MySQL tables using our schema"""
    conn = get_conn()
    cursor = conn.cursor()
    ut = (  "CREATE TABLE IF NOT EXISTS user ("
            "firstname TEXT NOT NULL, "
            "lastname TEXT NOT NULL, "
            "email VARCHAR(50) NOT NULL, "
            "institution TEXT NOT NULL, "
            "PRIMARY KEY (email) );")
    cursor.execute(ut)
    conn.commit()
    rt = (  "CREATE TABLE IF NOT EXISTS researches ("
            "email VARCHAR(50) NOT NULL, "
            "symbol VARCHAR(50) NOT NULL, "
            "PRIMARY KEY (email, symbol),"
            "FOREIGN KEY (email) REFERENCES user (email) "
            "ON DELETE CASCADE ON UPDATE CASCADE "
            ");")
    cursor.execute(rt)
    conn.commit()
    gst = ( "CREATE TABLE IF NOT EXISTS genesymbol ("
            "symbol VARCHAR(50) NOT NULL, "
            "name TEXT, "
            "species VARCHAR(255) DEFAULT 'homo_sapiens', "
            "url VARCHAR(255) DEFAULT 'www.genecards.org', "
            "PRIMARY KEY (symbol) );")
    cursor.execute(gst)
    conn.commit()
    gt = (  "CREATE TABLE IF NOT EXISTS gene ("
            "gid VARCHAR(80) NOT NULL, "
            "source VARCHAR(255) NOT NULL, "
            "symbol VARCHAR(50) NOT NULL, "
            "PRIMARY KEY (gid, source, symbol) );")
    cursor.execute(gt)
    conn.commit()
    conn.close()

def download_ensembl():
    """Downloads the gene information data from ensembl"""
    files = ['homo_sapiens_core_82_38.sql.gz', 'transcript.txt.gz',
             'external_db.txt.gz', 'gene.txt.gz', 'object_xref.txt.gz', 
             'translation.txt.gz', 'xref.txt.gz']
    url = "ftp://ftp.ensembl.org/pub/release-82/mysql/homo_sapiens_core_82_38/"
    for fl in files:
        print("Downloading: " + url + fl)
        req = urllib2.urlopen(url + fl)
        fl = '../data/mysql/' + fl
        with open(fl, 'w') as out:
            shutil.copyfileobj(req, out)
        with open(fl[:-3], 'w') as f_out:
            f_in = gzip.open(fl)
            shutil.copyfileobj(f_in, f_out)
        os.remove(fl)

def import_ensembl():
    """Imports the ensembl gene information"""
    
    #Variables
    import_flags = '--delete'
    tablefile = '../data/mysql/*.txt'
    sqlfile = "../data/mysql/homo_sapiens_core_82_38.sql"

    #Import data into temporary database
    cmd = [ 'mysql', '-u', USER, '-h', HOST, '--port', PORT,
            '--password=' + PASS, EDB, '<', sqlfile]
    subprocess.call(' '.join(cmd), shell=True)
    cmd = [ 'mysqlimport', '-u', USER, '-h', HOST, '--port', PORT,
            '--password=' + PASS, import_flags, '--fields_escaped_by=\\\\',
            EDB, '-L', tablefile]
    subprocess.call(' '.join(cmd), shell=True)

def combine_tables():
    """Combine ensembl tables and query the relevant info into a new one"""
    steps = ['transcript', 'translation']
    conn = get_conn(HOST, PORT, USER, PASS, EDB)
    cursor = conn.cursor()
    cmd = ("CREATE TABLE IF NOT EXISTS all_mappings " + get_insert_cmd('gene')
            + ";")
    cursor.execute(cmd)
    conn.commit()
    for step in steps:
        cmd = ("INSERT INTO all_mappings " + get_insert_cmd(step) + ";")
        cursor.execute(cmd)
        conn.commit()

def populate_gene_tables():
    conn = get_conn(HOST, PORT, USER, PASS, EDB)
    cursor = conn.cursor()
    print("Creating genesymbol table")
    cmd = ( "INSERT INTO dnatabas_cs411.genesymbol "
            "SELECT DISTINCT all_mappings.display_label AS symbol, "
            "SUBSTRING_INDEX(all_mappings.description, '[', 1)  AS name, "
            "'homo_sapiens' AS species, "
            "CONCAT('http://www.genecards.org/cgi-bin/carddisp.pl?gene=', "
            "all_mappings.display_label) AS url "
            "FROM dnatabas_ensembl.all_mappings "
            "WHERE all_mappings.db_display_name = 'HGNC Symbol'"
            "GROUP BY symbol HAVING COUNT(symbol) = 1;")
    cursor.execute(cmd)
    conn.commit()
    print("Creating stable2symbol table")
    cmd = ( "CREATE TEMPORARY TABLE IF NOT EXISTS "
            "dnatabas_ensembl.stable2symbol AS ("
            "SELECT DISTINCT all_mappings.stable_id, "
            "all_mappings.display_label "
            "FROM dnatabas_ensembl.all_mappings "
            "WHERE all_mappings.db_display_name = 'HGNC Symbol');")
    cursor.execute(cmd)
    conn.commit()
    print("Creating gene table")
    cmd = ( "INSERT INTO dnatabas_cs411.gene "
            "SELECT DISTINCT all_mappings.dbprimary_acc as gid, "
            "all_mappings.db_name as source, "
            "stable2symbol.display_label AS symbol "
            "FROM all_mappings, stable2symbol "
            "WHERE all_mappings.stable_id = stable2symbol.stable_id;")
    cursor.execute(cmd)
    conn.commit()


def get_insert_cmd(step):
    """Returns the command to be used with an insert for the provided step."""
    if step == 'gene':
        cmd = ("SELECT DISTINCT xref.dbprimary_acc, xref.display_label, "
               "external_db.db_name,  external_db.db_display_name, "
               "gene.description, gene.stable_id "
               "FROM xref INNER JOIN external_db "
               "ON xref.external_db_id = external_db.external_db_id "
               "INNER JOIN object_xref "
               "ON xref.xref_id = object_xref.xref_id "
               "INNER JOIN gene "
               "ON object_xref.ensembl_id = gene.gene_id "
               "WHERE object_xref.ensembl_object_type = 'Gene'")
    elif step == 'transcript':
        cmd = ("SELECT DISTINCT xref.dbprimary_acc, xref.display_label, "
               "external_db.db_name,  external_db.db_display_name, "
               "gene.description, gene.stable_id "
               "FROM xref INNER JOIN external_db "
               "ON xref.external_db_id = external_db.external_db_id "
               "INNER JOIN object_xref "
               "ON xref.xref_id = object_xref.xref_id "
               "INNER JOIN transcript "
               "ON object_xref.ensembl_id = transcript.transcript_id "
               "INNER JOIN gene "
               "ON transcript.gene_id = gene.gene_id "
               "WHERE object_xref.ensembl_object_type = 'Transcript'")
    elif step == 'translation':
        cmd = ("SELECT DISTINCT xref.dbprimary_acc, xref.display_label, "
               "external_db.db_name,  external_db.db_display_name, "
               "gene.description, gene.stable_id "
               "FROM xref INNER JOIN external_db "
               "ON xref.external_db_id = external_db.external_db_id "
               "INNER JOIN object_xref "
               "ON xref.xref_id = object_xref.xref_id "
               "INNER JOIN translation "
               "ON object_xref.ensembl_id = translation.translation_id "
               "INNER JOIN transcript "
               "ON translation.transcript_id = transcript.transcript_id "
               "INNER JOIN gene "
               "ON transcript.gene_id = gene.gene_id "
               "WHERE object_xref.ensembl_object_type = 'Translation'")
    else:
        cmd = ''
    return cmd

if __name__ == "__main__":
    create_tables()
    download_ensembl()
    import_ensembl()
    combine_tables()
    populate_gene_tables()
    with open('complete.txt', 'w') as outfile:
        outfile.write('done')

