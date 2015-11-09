"""
This downloads the literature citations for each gene and produces a tsv
file which can be used for producing the suggestion trie.
"""

import mysql_utilities as mu
import urllib2
import shutil
import subprocess
import os
import gzip
import csv
import contextlib

def download_gene2pubmed():
    """Downloads the gene pubmed information"""
    fl = 'gene2pubmed.gz'
    url = 'ftp://ftp.ncbi.nih.gov/gene/DATA/'
    print("Downloading: " + url + fl)
    req = urllib2.urlopen(url + fl)
    fl = '../data/' + fl
    with open(fl, 'w') as out:
        shutil.copyfileobj(req, out)
    with open(fl[:-3], 'w') as f_out:
        f_in = gzip.open(fl)
        shutil.copyfileobj(f_in, f_out)
    os.remove(fl)

def map_gene_id(geneid, db=mu.get_conn()):
    """Maps the provided gene id to a gene symbol using the MySQL db"""
    cursor = db.cursor()
    cmd = ("SELECT gene.symbol "
           "FROM gene "
           "WHERE gene.source = 'EntrezGene' "
           "AND gene.gid = '" + geneid + "'")
    cursor.execute(cmd)
    return cursor.fetchall()

def process_download(fl='../data/gene2pubmed'):
    """Counts all of the citations for each human gene in the gene2
    pubmed file and maps gene ids to gene symbols. Then writes the
    results to disk."""
    db = mu.get_conn()
    curr = ''
    count = 0
    scores = dict()
    with contextlib.nested(open(fl), open('../data/citations.tsv', 'w')) as \
        (infile, outfile):
        reader = csv.reader(infile, delimiter='\t')
        writer = csv.writer(outfile, delimiter='\t')
        next(reader, None)
        for row in reader:
            (taxid, gid, pmid) = row
            if taxid != '9606': continue #if wrong species
            if curr == '': #if first time human is seen
                curr = gid
                count = 1
            elif gid != curr: #if switching to a new gene id
                slist = map_gene_id(curr, db)
                for tuple in slist:
                    symbol = tuple[0].decode()
                    if symbol not in scores:
                        scores[symbol] = count
                    else:
                        scores[symbol] += count
                curr = gid
                count = 1
            else: #if another publication for the same gene id is seen
                count += 1

        slist = map_gene_id(curr, db) #for the last human gene
        for tuple in slist:
            symbol = tuple[0].decode()
            if symbol not in scores:
                scores[symbol] = count
            else:
                scores[symbol] += count

        for symbol in scores: #write out the results
                writer.writerow([symbol, str(scores[symbol])])

if __name__ == "__main__":
    download_gene2pubmed()
    process_download()
