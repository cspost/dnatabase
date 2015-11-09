"""
This generates all of the typos for the provided gene symbol set and outputs
the table of typo to most cited actual gene symbol.
"""

import get_literature_scores as gts
import os
import csv

ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'

def get_key_coords():
    """Gives the coordinates for each key on the keyboard"""
    keys = "`1234567890-= | qwertyuiop[]\| asdfghjkl;'  | zxcvbnm,./   "
    rows = keys.split('|')
    keymap = list()
    coords = dict()
    for i in range(0, len(rows)):
        keymap.append([])
        for j in range(0, len(rows[i])):
            letter = rows[i][j].upper()
            keymap[i].append(letter)
            if letter == ' ': continue
            coords[letter] = (i, j)
    return coords, keymap

def get_typo_keys(key_coords, keymap, letter):
    """Returns all of the keys which are adjacent or diagnol to the given key
    on a QWERTY keyboard"""
    letter = letter.upper()
    if letter not in key_coords:
        return list()
    (x, y) = key_coords[letter]
    typos = list()
    for i in range(x-1, x+2):
        if i < 0 or i >= len(keymap): continue
        for j in range(y-1, y+2):
            if j < 0 or j >= len(keymap[i]): continue
            new_letter = keymap[i][j]
            if new_letter == ' ': continue
            typos.append(new_letter)
    return typos

def edits(key_coords, keymap, word):
    """Generates all of the spelling deletions, transposes, replaces, and
    insertions for the provided word. Based on the Norvig spelling corrector
    found here http://norvig.com/spell-correct.html. It has been modified to
    include only letters close on the keyboard for replaces/inserts."""
    typos = list()
    for i in range(0, len(word)):
        if (i < len(word)): #deletions
            typos.append(word[:i] + word[i+1:])
        if (i+1 < len(word)): #tranpositions
            typos.append(word[:i] + word[i+1] + word[i] + word[i+2:])
        for char in get_typo_keys(key_coords, keymap, word[i]):
            if (i < len(word) and word[i] != char): #replacements
                typos.append(word[:i] + char + word[i+1:])
            if (char in ALPHABET): #insertions
                typos.append(word[:i] + char + word[i:]) #insertions
    return set(typos)

def process_edits(key_coords, keymap, genefile):
    """Generates all of the typos and assigns the correct real symbol to each"""
    gene_scores = dict()
    max_scores = dict()
    with open(genefile) as infile:
        reader = csv.reader(infile, delimiter='\t')
        for line in reader:
            gene_scores[line[0]] = int(line[1])
    for symbol in gene_scores:
        typos = edits(key_coords, keymap, symbol)
        score = gene_scores[symbol]
        for edit in typos:
            if edit in gene_scores: 
                continue
            if edit in max_scores:
                (sym, val) = max_scores[edit]
                if val < score:
                    max_scores[edit] = (symbol, score)
            else:
                max_scores[edit] = (symbol, score)
    write_typo_file(max_scores)

def write_typo_file(typo_dict):
    """Writes the provided typo dictionary to an outfile"""
    with open('../data/typos.tsv', 'w') as outfile:
        writer = csv.writer(outfile, delimiter='\t')
        for typo in sorted(typo_dict):
            writer.writerow([typo, typo_dict[typo][0]])

if __name__ == "__main__":
    (key_coords, keymap) = get_key_coords()
    gene_file = '../data/citations.tsv'
    if not os.path.isfile(gene_file):
        gts.download_gene2pubmed()
        gts.process_download()
    process_edits(key_coords, keymap, gene_file)
