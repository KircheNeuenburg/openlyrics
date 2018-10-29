import { isArray } from "util";

export class openlyrics
{
    constructor(xml_content)
    {
        this.xml = xml_content
        
        let dom_parser = new DOMParser();
        this.song_dom = dom_parser.parseFromString(this.xml, "text/xml");

        if(this.song_dom.documentElement.nodeName !== "parsererror")
        {
        
            
            this.song_dom = this.song_dom.getElementsByTagName('song').item(0);
            this.process_metadata();
            this.process_properties();
            this.process_lyrics();
        }

    }

    process_metadata()
    {
        this.metadata = new Object()
        this.metadata.version = this.song_dom.getAttribute('version');
        this.metadata.created_in = this.song_dom.getAttribute('createdIn');
        this.metadata.modified_in = this.song_dom.getAttribute('modifiedIn');
        this.metadata.modified_date = this.song_dom.getAttribute('modifiedDate');
    }

    process_properties()
    {
        this.properties = new Object()
        this.process_titles();
        this.process_authors();
        this.process_copyright();
        this.process_ccli_number();
        this.process_release_date();
        this.process_transposition();
        this.process_tempo();
        this.process_key();
        this.process_variant();
        this.process_publisher();
        this.process_version();
        this.process_keywords();
        this.process_verse_order();
        this.process_songbooks() ;
        this.process_themes() ;
        this.process_comments(); 
    }

    process_titles() 
    {    
        this.properties.titles = new Array()
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let titles = properties.getElementsByTagName('title');
        for( let title of titles)
        {
            let titleObject = new Object()
            titleObject.value = title.innerHTML;
            titleObject.lang = title.getAttribute('lang');
            titleObject.original = title.getAttribute('original');

            this.properties.titles.push(titleObject) 
        }
        if( this.properties.titles.length === 0)
        {
            let titleObject = new Object()
            titleObject.value = '';
            titleObject.lang = '';
            titleObject.original = '';

            this.properties.titles.push(titleObject)
        }   
    } 

    process_authors()
    {
        this.properties.authors = new Array()
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let authors = properties.getElementsByTagName('author');
        for( let author of authors)
        {
            let authorObject = new Object()
            authorObject.value = author.innerHTML;
            authorObject.lang = author.getAttribute('lang');
            authorObject.type = author.getAttribute('type');

            this.properties.authors.push(authorObject)
        }
        if(this.properties.authors.length === 0)
        {
            let authorObject = new Object()
            authorObject.value = '';
            authorObject.lang = '';
            authorObject.type = '';

            this.properties.authors.push(authorObject)
        }
    }
  
    process_copyright()
    {
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let copyright = properties.getElementsByTagName('copyright').item(0);
        
        if( copyright !== null)
        {
            this.properties.copyright = copyright.innerHTML;
        }
        else
        {
            this.properties.copyright = '';
        }
    }
    
    process_ccli_number()
    {
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let ccli_number = properties.getElementsByTagName('ccliNo').item(0);
        if(null !== ccli_number)
        {
            this.properties.ccli_number = ccli_number.innerHTML;
        }
        else
        {
            this.properties.ccli_number = '';
        }
    }

    process_release_date()
    {
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let release_date = properties.getElementsByTagName('released').item(0);
        if(null !== release_date)
        {
            this.properties.release_date = release_date.innerHTML;
        }
        else
        {
            this.properties.release_date = '';
        }
    }

    process_transposition()
    {
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let transposition = properties.getElementsByTagName('transposition').item(0);
        if(null !== transposition)
        {
            this.properties.transposition = transposition.innerHTML;
        }
        else
        {
            this.properties.transposition = '';
        }
    }

    process_tempo()
    {
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let tempo = properties.getElementsByTagName('tempo').item(0);
        this.properties.tempo = new Object();
        if(null !== tempo)
        { 
            this.properties.tempo.value = tempo.innerHTML;
            this.properties.tempo.type = tempo.getAttribute('type');
        }
        else
        {
            this.properties.tempo.value = '';
            this.properties.tempo.type = '';
        }

    }

    process_key()
    {
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let key = properties.getElementsByTagName('key').item(0);
        if(null !== key)
        {
            this.properties.key = key.innerHTML;
        }
        else
        {
            this.properties.copyright = '';
        }
    }

    process_variant()
    {
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let variant = properties.getElementsByTagName('variant').item(0);
        if(null !== variant)
        {
            this.properties.variant = variant.innerHTML;
        }
        else
        {
            this.properties.variant = '';
        }
    }

    process_publisher()
    {
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let publisher = properties.getElementsByTagName('publisher').item(0);
        if(null !== publisher)
        {
            this.properties.publisher = publisher.innerHTML;
        }
        else
        {
            this.properties.publisher = '';
        }
    }

    process_version()
    {
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let version = properties.getElementsByTagName('version').item(0);
        if(null !== version)
        {
            this.properties.version = version.innerHTML;
        }
        else
        {
            this.properties.version = '';
        }
    }

    process_keywords()
    {
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let keywords = properties.getElementsByTagName('keywords').item(0);
        if(null !== keywords)
        {
            this.properties.keywords = keywords.innerHTML;
        }
        else
        {
            this.properties.keywords = '';
        }
    }

    process_verse_order()
    {
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let verse_order = properties.getElementsByTagName('verseOrder').item(0);
        if(null !== verse_order)
        {
            this.properties.verse_order = verse_order.innerHTML;
        }
        else
        {
            this.properties.verse_order = '';
        }
    }

    process_songbooks() 
    {    
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let songbooks = properties.getElementsByTagName('songbook');
        this.properties.songbooks = new Array();
        for( let songbook of songbooks)
        {
            let songbookObject = new Object
            songbookObject.name = songbook.getAttribute('name');
            songbookObject.entry = songbook.getAttribute('entry');

            this.properties.songbooks.push(songbookObject);
        }
        if( this.properties.songbooks.length === 0)
        {
            let songbookObject = new Object
            songbookObject.name = '';
            songbookObject.entry = '';

            this.properties.songbooks.push(songbookObject);   
        }
    }

    process_themes() 
    {    
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let themes = properties.getElementsByTagName('theme');
        this.properties.themes = new Array();
        for( let theme of themes)
        {
            let themeObject = new Object();
            themeObject.value = theme.innerHTML;
            themeObject.lang = theme.getAttribute('lang');

            this.properties.themes.push(themeObject);
        }   
        if( this.properties.themes.length === 0)
        {
            let themeObject = new Object
            themeObject.value = '';
            themeObject.lang = '';

            this.properties.themes.push(themeObject)
        }
    } 

    process_comments() 
    {    
        let properties = this.song_dom.getElementsByTagName('properties').item(0);
        let comments = properties.getElementsByTagName('comment');
        this.properties.comments = new Array()
        for( let comment of comments)
        {
            this.properties.comments.push(comment.innerHTML);
        }   
        if( this.properties.comments.length === 0)
        {
            this.properties.comments.push('');
        }
        
    } 

    process_lyrics()
    {
        let lyrics = this.song_dom.getElementsByTagName('lyrics').item(0);
        let verses = lyrics.getElementsByTagName('verse');
        this.lyrics = new Object()
        this.lyrics.verses = new Array()

        for( let verse of verses) { 

            let verseObject = new Object()
            verseObject.name = verse.getAttribute('name');
            verseObject.lang = verse.getAttribute('lang');
            verseObject.translit = verse.getAttribute('translit');
            verseObject.lines = new Array()

            let lines = verse.getElementsByTagName('lines');

            for( let line of lines)
            {
                let line_text = this.process_lines_mixed_content(line);
                if(line.getAttribute('break')=== 'optional')
                {
                    line_text += "\n" + '[---]';
                }
                verseObject.lines.push(line_text)
                 
            }
            
            this.lyrics.verses.push(verseObject)
        }
        if(this.lyrics.verses.length === 0)
        {
            verseObject.name = '';
            verseObject.lang = '';
            verseObject.translit = '';
            verseObject.lines.push('')
            this.lyrics.verses.push(verseObject)
        }
    }

    process_lines_mixed_content(line_children)
    {
        let text = '';

        for(let child of line_children.childNodes)
        {
            let use_endtag = true;
            let use_endcomment = false;

            if(child.nodeName === 'comment')
            {
                use_endcomment = true;
            }
            else if(child.nodeName === 'chord')
            {
                text += '[' + child.getAttribute('name') + ']';
            }
            else if(child.nodeName === 'br')
            {
                text += "\n";
            }
            else if(child.nodeName === 'tag')
            {
                text += '{'+child.getAttribute('name') + '}';
                if(!child.firstChild && child.textContent === '')
                {
                    use_endtag = false;
                }
            }
            else if(child.nodeName === '#text')
            {
                text += child.textContent;
            }
            if(use_endcomment == false)
            {
                text += this.process_lines_mixed_content(child);
            }
            if(child.nodeName === 'tag' && use_endtag)
            {
                text += '{/' + child.getAttribute('name') + '}';
                use_endtag = true;
            }    
        }
        return text;
    }

    export_xml()
    {
        this.song_dom_export = new Document();
        let song = this.song_dom_export.createElement('song')


        
        let attr_xmlns = this.song_dom_export.createAttribute('xmlns');
        attr_xmlns.value = 'http://openlyrics.info/namespace/2009/song';
        song.setAttributeNode(attr_xmlns);

        let attr_version = this.song_dom_export.createAttribute('version');
        attr_version.value = '0.8';
        song.setAttributeNode(attr_version);

        let attr_created_in = this.song_dom_export.createAttribute('createdIn');
        attr_created_in.value = this.metadata.created_in;
        song.setAttributeNode(attr_created_in);

        let attr_modified_in = this.song_dom_export.createAttribute('modifiedIn');
        attr_modified_in.value = 'OpenLyrics 0.1.0';
        song.setAttributeNode(attr_modified_in);

        let attr_modified_date = this.song_dom_export.createAttribute('modifiedDate');
        let current_date = new Date()
        attr_modified_date.value = current_date.toISOString();
        song.setAttributeNode(attr_modified_date);
        

        song.appendChild(this.export_properties(this.song_dom_export));
        song.appendChild(this.export_lyrics(this.song_dom_export));
        this.song_dom_export.appendChild(song);
        this.song_dom_export.formatOutput = true;
        let song_export = new XMLSerializer()

        return song_export.serializeToString(this.song_dom_export)
    }

    export_properties(dom)
    {
        let properties = dom.createElement('properties');

        properties.appendChild(this.export_titles(dom));

        properties.appendChild(this.export_authors(dom));

        if(this.properties.copyright!= '')
        {
            properties.appendChild(this.export_copyright(dom));
        }
        if(this.properties.ccli_number != '')
        {
            properties.appendChild(this.export_ccli_number(dom));
        }
        if(this.properties.release_date != '')
        {
            properties.appendChild(this.export_release_date(dom));
        }
        if(this.properties.transposition != '')
        {
            properties.appendChild(this.export_transposition(dom));
        }
        if(this.properties.tempo != '')
        {
            properties.appendChild(this.export_tempo(dom));
        }
        if(this.properties.key != '')
        {
            properties.appendChild(this.export_key(dom));
        }
        if(this.properties.variant != '')
        {
            properties.appendChild(this.export_variant(dom));
        }
        if(this.properties.publisher != '')
        {
            properties.appendChild(this.export_publisher(dom));
        }
        if(this.properties.version != '')
        {
            properties.appendChild(this.export_version(dom));
        }
        if(this.properties.keywords != '')
        {
            properties.appendChild(this.export_keywords(dom));
        }
        if(this.properties.verse_order != '')
        {
            properties.appendChild(this.export_verse_order(dom));
        }
        properties.appendChild(this.export_songbooks(dom));
        properties.appendChild(this.export_themes(dom));
        properties.appendChild(this.export_comments(dom));
        
        return properties;
    }

    export_titles(dom)
    {
        let titles = dom.createElement('titles');
        for(let title of this.properties.titles)
        {
            let title_el = dom.createElement('title',title.value);

            if(title.original == 'true')
            {
                let attr_original = dom.createAttribute('original');
                attr_original.value = title.original;
                title_el.setAttributeNode(attr_original);
            }
            if(title.lang != '')
            {
                let attr_lang = dom.createAttribute('lang');
                attr_lang.value = title.lang;
                title_el.setAttributeNode(attr_lang);
            }

            titles.appendChild(title_el);

        }
        return titles;
    }

    export_authors(dom)
    {
        let authors = dom.createElement('authors');
        for(let author of this.properties.authors)
        {
            let author_el = dom.createElement('author',author.value);
            if(author.type != '')
            {
                let attr_type = dom.createAttribute('type');
                attr_type.value = author.type;
                author_el.setAttributeNode(attr_type);
            }
            if(author.lang != '')
            {
                let attr_lang = dom.createAttribute('lang');
                attr_lang.value = author.lang;
                author_el.setAttributeNode(attr_lang);
            }
            authors.appendChild(author_el);

        }
        return authors;
    }

    export_copyright(dom)
    {
        return dom.createElement('copyright',this.properties.copyright);
    }

    export_ccli_number(dom)
    {
        return dom.createElement('ccliNo',this.properties.ccli_number);
    }

    export_release_date(dom)
    {
        return dom.createElement('released',this.properties.release_date);
    }

    export_transposition(dom)
    {
        return dom.createElement('transposition',this.properties.transposition);
    }

    export_tempo(dom)
    {
        let tempo = dom.createElement('tempo',this.properties.tempo.value);
        tempo.setAttribute('type',this.properties.tempo.type);
        return tempo;
    }
    export_key(dom)
    {
        return dom.createElement('key',this.properties.key);
    }
    export_variant(dom)
    {
        return dom.createElement('variant',this.properties.variant);
    }

    export_publisher(dom)
    {
        return dom.createElement('publisher',this.properties.publisher);
    }

    export_version(dom)
    {
        return dom.createElement('version',this.properties.version);
    }

    export_keywords(dom)
    {
        return dom.createElement('keywords',this.properties.keywords);
    }

    export_verse_order(dom)
    {
        return dom.createElement('verseOrder',this.properties.verse_order);
    }

    export_songbooks(dom)
    {
        let songbooks = dom.createElement('songbooks');
        for(let songbook of this.properties.songbooks)
        {
            let songbook_el = dom.createElement('songbook');
            if(songbook.name != '')
            {
                let attr_name = dom.createAttribute('name');
                attr_name.value = songbook.name;
                songbook_el.setAttributeNode(attr_name);
            }
            if(songbook.entry != '')
            {
                let attr_entry = dom.createAttribute('entry');
                attr_entry.value = songbook.entry;
                songbook_el.setAttributeNode(attr_entry);
            }
            songbooks.appendChild(songbook_el);
        }
        return songbooks;
    }

    export_themes(dom)
    {
        let themes = dom.createElement('themes');
        for(let theme of this.properties.themes)
        {
            let theme_el = dom.createElement('theme',theme.value);
            if(theme.lang != '')
            {
                let attr_lang = dom.createAttribute('lang');
                attr_lang.value = theme.lang;
                theme_el.setAttributeNode(attr_lang);
            }
            themes.appendChild(theme_el);
        }
        return themes;
    }

    export_comments(dom)
    {
        let comments = dom.createElement('comments');
        for(let comment of this.properties.comments)
        {
            let comment_el = dom.createElement('comment',comment);
            comments.appendChild(comment_el);
        }
        return comments;
    }

    export_lyrics(dom)
    {
        let lyrics =  dom.createElement('lyrics');
        for(let verse of this.lyrics.verses)
        {
            let verse_el = dom.createElement('verse');
            if(verse.name != '')
            {
                let attr_name = dom.createAttribute('name');
                attr_name.value = verse.name;
                verse_el.setAttributeNode(attr_name);
            }
            if(verse.lang != '')
            {
                let attr_lang = dom.createAttribute('lang');
                attr_lang.value = verse.lang;
                verse_el.setAttributeNode(attr_lang);
            }
            if(verse.translit != '')
            {
                let attr_translit = dom.createAttribute('translit');
                attr_translit.value = verse.translit;
                verse_el.setAttributeNode(attr_translit);
            }
            for(let line of verse.lines)
            {
                verse_el.appendChild(this.export_lines(dom,line));
            }
            
            lyrics.appendChild(verse_el);
        }
        return lyrics;
    }

    export_lines(dom, line)
    {
        let regex_start_tag = /\{(\w+)\}/g //preg_match_all('/\{(\w+)\}/',line,start_tags);
        let regex_end_tag = /\{\/(\w+)\}/g //preg_match_all('/\{\/(\w+)\}/',line,end_tags);
        let start_tags = line.match(/\{(\w+)\}/g)
        let end_tags = line.match(/\{\/(\w+)\}/g)

        let text = line
        if(start_tags)
        {
            for(let tag of start_tags)
            {   
                    
                if(end_tags.isArray && -1 !== end_tags.indexOf(tag))
                {
                            
                    text = text.replace('{' + tag + '}','<tag name="' + tag + '">')
                }
                else
                {
                    text = text.replace('{' + tag + '}','<tag name="' + tag + '"/>')
                }           
            }
        }
        if(end_tags)
        {
            for(let tag of end_tags)
            {       
                text = text.replace('{/' + tag + '}','</tag>')
            }
        }
        text = text.replace(/\r\n|\r|\n/g, '<br />')

        text = text.replace(/\[(\w.*?)\]/g, '<chord name="$1"/>')
        let dom_parser = new DOMParser()
        let tmp_dom = dom_parser.parseFromString('<lines>' + text +'</lines>', "text/xml");
        //tmp_dom.loadXML('<lines>' + text +'</lines>');
                                
        let lines = dom.importNode(tmp_dom.getElementsByTagName('lines').item(0),true);
                

        return lines;
    }

    add_title()
    {
        let titleObject = new Object()
        titleObject.value = '';
        titleObject.lang = '';
        titleObject.original = '';
        this.properties.titles.push(titleObject)
    }
    
}