export default (editor, config) => {
  const bm = editor.BlockManager;
  const toAdd = name => config.blocks.indexOf(name) >= 0;

  toAdd('link-block') && bm.add('link-block', {
    category: 'Basic',
    label: 'Link Block',
    attributes: { class: 'fa fa-link' },
    content: {
      type:'link',
      editable: false,
      droppable: true,
      style:{
        display: 'inline-block',
        padding: '5px',
        'min-height': '50px',
        'min-width': '100%'
      }
    },
  });

  toAdd('quote') && bm.add('quote', {
    label: 'Quote',
    category: 'Basic',
    attributes: { class: 'ca ca-quote' },
    content: `<blockquote class="quote">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore ipsum dolor sit
      </blockquote>`
  });
  toAdd('separator') && bm.add('separator', {
    label: 'Block separator',
    category: 'Basic',
    attributes: { class: 'ca ca-separator' },
    content: `<div data-gjs-droppable="false" class="ebs-separator cp007"><div data-gjs-droppable="false" class="ebs-breakpage"></div></div>`
  });
  toAdd('box-4') && bm.add('box-4', {
    label: '4 columns',
    category: 'Grid',
    attributes: { class: 'ca ca-4columns'},
    content: `<div class="ebs-row" draggable="true">
    <div class="ebs-grid eb-3 cp109" draggable="true"></div>
    <div class="ebs-grid eb-3 cp109" draggable="true"></div>
    <div class="ebs-grid eb-3 cp109" draggable="true"></div>
    <div class="ebs-grid eb-3 cp109" draggable="true"></div>
  </div>`
  });
  toAdd('box-34') && bm.add('box-34', {
    label: '8-4 columns',
    category: 'Grid',
    attributes: { class: 'ca ca-84columns'},
    content: `<div class="ebs-row" draggable="true">
    <div class="ebs-grid eb-8 cp119" draggable="true"></div>
    <div class="ebs-grid eb-4 cp119" draggable="true"></div>
  </div>`
  });
  toAdd('box-24') && bm.add('box-24', {
    label: '6-2 Columns',
    category: 'Grid',
    attributes: { class: 'ca ca-62columns'},
    content: `<div class="ebs-row" draggable="true">
    <div class="ebs-grid eb-6 cp119" draggable="true"></div>
    <div class="ebs-grid eb-2 cp119" draggable="true"></div>
    <div class="ebs-grid eb-2 cp119" draggable="true"></div>
    <div class="ebs-grid eb-2 cp119" draggable="true"></div>
  </div>`
  });
  toAdd('section-box') && bm.add('section-box', {
    label: 'Section',
    category: 'Grid',
    attributes: { class: 'ca ca-section'},
    content: `<div class="ebs-section" draggable="true"><div class="ebs-container" draggable="true"></div></div>`
  });
  toAdd('table') && bm.add('table', {
    label: 'Table',
    category: 'Basic',
    attributes: { class: 'ca ca-table' },
    content: `<table class="table" width="100%"><tr><td>insert table</td><td>insert table</td><td>insert table</td></tr></table>`
  });


  toAdd('text-basic') && bm.add('text-basic', {
    category: 'Basic',
    label: 'Text section',
    attributes: { class: 'ca ca-textsection' },
    content: `<section class="bdg-sect">
      <h1 class="heading">Insert title here</h1>
      <p class="paragraph">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
      </section>`
  });
  toAdd('button-panel') && bm.add('button-panel', {
    category: 'Basic',
    label: 'Buttons Group',
    attributes: { class: 'ca ca-button' },
    content: `<div class="gp-button-panel">
    <div class="gp-btn"><a class="gp-link" href="">Primary Button</a></div>
    <div class="gp-btn"><a class="gp-link gp-secondry" href="">Secondry Button</a></div>
  </div>`
  });
  toAdd('social-panel') && bm.add('social-panel', {
    category: 'Basic',
    label: 'Share Links 1',
    attributes: { class: 'ca ca-share' },
    content: `<div class="gp-social-panel">
    <a class="gps-link op147" href=""><img class="op-icon op146" src="./img/facebook.svg" alt="Facebook" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op146" src="./img/twitter.svg" alt="Twitter" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op146" src="./img/instagram.svg" alt="" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op146" src="./img/whatsapp.svg" alt="" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op146" src="./img/youtube.svg" alt="" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op146" src="./img/be.svg" alt="" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op146" src="./img/rss.svg" alt="" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op146" src="./img/instagram.svg" alt="" /></a>
  </div>`
  });
  toAdd('social-panel-2') && bm.add('social-panel-2', {
    category: 'Basic',
    label: 'Share Links 2',
    attributes: { class: 'ca ca-share' },
    content: `<div class="gp-social-panel">
    <a class="gps-link op147" href=""><img class="op-icon op144" src="./img/facebook-1.svg" alt="Facebook" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op144" src="./img/twitter-1.svg" alt="Twitter" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op144" src="./img/instagram-1.svg" alt="" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op144" src="./img/whatsapp-1.svg" alt="" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op144" src="./img/youtube-1.svg" alt="" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op144" src="./img/be-1.svg" alt="" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op144" src="./img/rss-1.svg" alt="" /></a>
    <a class="gps-link op147" href=""><img class="op-icon op144" src="./img/instagram-1.svg" alt="" /></a>
  </div>`
  });
  toAdd('text-hero') && bm.add('text-hero', {
    category: 'Components',
    label: 'Hero',
    attributes: { class: 'ca ca-hero' },
    content: `<div class="row gp-hero">
        <div class="cell gp-hero-container">
          <h3 class="gp-heading">Centered hero</h3>
          <div class="gp-text">Quickly design and customize responsive mobile-first sites with Bootstrap, the world’s most popular front-end open source toolkit, featuring Sass variables and mixins, responsive grid system, extensive prebuilt components, and powerful JavaScript plugins.</div>
          <div class="gp-button-panel">
            <div class="gp-btn"><a class="gp-link" href="">Primary Button</a></div>
            <div class="gp-btn"><a class="gp-link gp-secondry" href="">Secondry Button</a></div>
          </div>
        </div> 
      </div>`
  });
  
  toAdd('pricing-table') && bm.add('pricing-table', {
    category: 'Components',
    label: 'Pricing Table',
    attributes: { class: 'ca ca-ptable' },
    content: `<div class="row gp-pricing">
    <div class="cell gp-pricing-container">
     <div class="gp-item-row">
      <div class="gp-grid opprice">
        <div class="gp-pricing-wrapp">
         <div class="gp-title">Title</div>
          <div class="gb-body">
            <h3 class="gb-pricing-title">$0<small class="gb-light">/month</small></h3>
            <ul class="gb-list">
              <li>10 users included</li>
              <li>2 GB of storage</li>
              <li>Email support</li>
              <li>Help center access</li>
            </ul>
            <div class="gp-button-panel">
              <div class="gp-btn"><a class="gp-link" href="">Primary Button</a></div>
            </div>
          </div> 
        </div> 
      </div>
      <div class="gp-grid opprice">
        <div class="gp-pricing-wrapp">
         <div class="gp-title">Title</div>
          <div class="gb-body">
            <h3 class="gb-pricing-title">$0<small class="gb-light">/month</small></h3>
            <ul class="gb-list">
              <li>10 users included</li>
              <li>2 GB of storage</li>
              <li>Email support</li>
              <li>Help center access</li>
            </ul>
            <div class="gp-button-panel">
              <div class="gp-btn"><a class="gp-link" href="">Primary Button</a></div>
            </div>
          </div> 
        </div> 
      </div>
      <div class="gp-grid opprice">
        <div class="gp-pricing-wrapp">
         <div class="gp-title">Title</div>
          <div class="gb-body">
            <h3 class="gb-pricing-title">$0<small class="gb-light">/month</small></h3>
            <ul class="gb-list">
              <li>10 users included</li>
              <li>2 GB of storage</li>
              <li>Email support</li>
              <li>Help center access</li>
            </ul>
            <div class="gp-button-panel">
              <div class="gp-btn"><a class="gp-link gp-secondry" href="">Secondry Button</a></div>
            </div>
          </div> 
        </div> 
      </div>
     </div>
    </div> 
  </div>`
  });
  toAdd('block-album') && bm.add('block-album', {
    category: 'Components',
    label: 'Album',
    attributes: { class: 'ca ca-album' },
    content: `<div class="row gp-album">
        <div class="cell gp-album-container">
         <div class="gp-item-row">
          <div class="gp-grid opalbum">
            <div class="gp-grid-wrapp">
              <figure class="gp-figure">
                <img src="https://via.placeholder.com/800X550/55595c/FFFFFFF/?text=Thumbnail" alt="" />
              </figure>
              <div class="gb-caption">
              <p>This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
              </div> 
            </div> 
          </div>
          <div class="gp-grid opalbum">
            <div class="gp-grid-wrapp">
              <figure class="gp-figure">
                <img src="https://via.placeholder.com/800X550/55595c/FFFFFFF/?text=Thumbnail" alt="" />
              </figure>
              <div class="gb-caption">
              <p>This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
              </div> 
            </div> 
          </div>
          <div class="gp-grid opalbum">
            <div class="gp-grid-wrapp">
              <figure class="gp-figure">
                <img src="https://via.placeholder.com/800X550/55595c/FFFFFFF/?text=Thumbnail" alt="" />
              </figure>
              <div class="gb-caption">
              <p>This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
              </div> 
            </div> 
          </div>
         </div>
        </div> 
      </div>`
  });
  toAdd('team-cards') && bm.add('team-cards', {
    category: 'Components',
    label: 'Team Cards',
    attributes: { class: 'ca ca-tcards' },
    content: `<div class="row gp-album">
        <div class="cell gp-album-container">
         <div class="gp-item-row">
          <div class="gp-grid op-pb-20 cpa999 textcenter">
              <figure class="gp-figure">
                <img src="https://via.placeholder.com/600X700/55595c/FFFFFFF/?text=Thumbnail" alt="" />
              </figure>
              <h3>title</h3>
              <p>Caption</p>
          </div>
          <div class="gp-grid op-pb-20 cpa999 textcenter">
              <figure class="gp-figure">
                <img src="https://via.placeholder.com/600X700/55595c/FFFFFFF/?text=Thumbnail" alt="" />
              </figure>
              <h3>title</h3>
              <p>Caption</p>
          </div>
          <div class="gp-grid op-pb-20 cpa999 textcenter">
              <figure class="gp-figure">
                <img src="https://via.placeholder.com/600X700/55595c/FFFFFFF/?text=Thumbnail" alt="" />
              </figure>
              <h3>title</h3>
              <p>Caption</p>
          </div>
         </div>
        </div> 
      </div>`
  });
  toAdd('block-cards') && bm.add('block-cards', {
    category: 'Components',
    label: 'Cards',
    attributes: { class: 'ca ca-cards' },
    content: `<div class="row gp-album">
        <div class="cell gp-album-container">
         <div class="gp-item-row">
          <div class="gp-grid opalbum">
            <div class="gp-grid-wrapp">
              <div class="gb-caption textcenter">
              <h3>Heading</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse.</p>
              </div> 
              </div> 
              </div>
              <div class="gp-grid opalbum">
              <div class="gp-grid-wrapp">
              <div class="gb-caption textcenter">
              <h3>Heading</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse.</p>
              </div> 
              </div> 
              </div>
              <div class="gp-grid opalbum">
              <div class="gp-grid-wrapp">
              <div class="gb-caption textcenter">
              <h3>Heading</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse.</p>
              </div> 
            </div> 
          </div>
         </div>
        </div> 
      </div>`
  });
  toAdd('block-image-caption') && bm.add('block-image-caption', {
    category: 'Components',
    label: 'Image With Caption',
    attributes: { class: 'ca ca-caption-lg' },
    content: `<div class="row gp-image-caption eb1448">
        <div class="cell gp-album-container eb1447">
         <div class="gp-item-row">
          <div class="gp-block-grid opimgcaption">
            <div class="gp-image-wrapp eb1445">
              <figure class="gp-figure">
                <img src="https://via.placeholder.com/800X550/55595c/FFFFFFF/?text=Thumbnail" alt="" />
              </figure>
            </div> 
          </div>
          <div class="gp-block-grid opimgcaption">
            <div class="gp-image-wrapp eb1445">
              <div class="gb-image-caption eb1446">
              <h3>What is a component?</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse.</p>
              </div> 
            </div> 
          </div>
         </div>
        </div> 
      </div>`
  });
  toAdd('small-image-caption') && bm.add('block-small-caption', {
    category: 'Components',
    label: 'Image With Caption Small',
    attributes: { class: 'ca ca-caption' },
    content: `<div class="row gp-image-caption eb1448">
        <div class="cell gp-album-container eb1447">
         <div class="gp-item-row">
          <div class="gp-block-grid op-pb-20 cpp225">
           <div class="gp-item-row">
            <div class="gp-grid-box valign-middle cpo124">
            <img src="https://via.placeholder.com/800X550/55595c/FFFFFFF/?text=Thumbnail" alt="" />
            </div>
            <div class="gp-grid-box valign-middle gp-grid-caption cpo124">
            <div class="op-items"><p>Lorem ipsum dolor sit amet, consectetur </div></p>
            </div>
           </div>
          </div>
          <div class="gp-block-grid op-pb-20 cpp225">
           <div class="gp-item-row">
            <div class="gp-grid-box valign-middle cpo124">
            <img src="https://via.placeholder.com/800X550/55595c/FFFFFFF/?text=Thumbnail" alt="" />
            </div>
            <div class="gp-grid-box valign-middle gp-grid-caption cpo124">
              <div class="op-items"><p>Lorem ipsum dolor sit amet, consectetur </div></p>
            </div>
           </div>
          </div>
         </div>
        </div> 
      </div>`
  });
  toAdd('block-image-caption-2') && bm.add('block-image-caption-2', {
    category: 'Components',
    label: 'Image With Caption 2',
    attributes: { class: 'ca ca-caption2' },
    content: `<div class="row gp-image-caption eb1448">
        <div class="cell gp-album-container eb1447">
         <div class="gp-item-row">
          <div class="gp-block-grid op-width-100 opimgcaption">
            <div class="gp-image-wrapp eb1445">
              <figure class="gp-figure">
                <img src="https://via.placeholder.com/1500X500/55595c/FFFFFFF/?text=Thumbnail" alt="" />
              </figure>
            </div> 
          </div>
          <div class="gp-block-grid op-width-100 opimgcaption">
            <div class="gp-image-wrapp eb1445">
              <div class="gb-image-caption eb1446">
              <h3>What is a component?</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse.</p>
              </div> 
            </div> 
          </div>
         </div>
        </div> 
      </div>`
  });
  toAdd('block-footer') && bm.add('block-footer', {
    category: 'Components',
    label: 'Footer',
    attributes: { class: 'ca ca-footer' },
    content: `<div class="gp-footer-wrapp eb215">
      <div class="row gp-footer-1 eb2214">
        <div class="cell gp-album-container eb1417">
         <div class="gp-item-row">
          <div class="gp-footer-grid gp-col-1 eb2414">
            <div class="eb4445">
               <img class="eb4446" src="https://via.placeholder.com/300X100/55595c/FFFFFFF/?text=Thumbnail" alt="" />
               <p>Greenline.com Limited is registered in England and Wales. Company No. 3846791.</p>
               <p>
               Registered address: <br>
               3rd floor, 120 Holborn, London EC1N 2TD, United Kingdom. VAT number: 791^^ 7261 06.
               </p>
            </div> 
          </div>
          <div class="gp-footer-grid gp-col-2 eb2415">
            <ul class="op-list c656">
            <li><a class="gp-link-list" href="">About Greenline</a></li>
            <li><a class="gp-link-list" href="">News</a></li>
            <li><a class="gp-link-list" href="">Investors</a></li>
            <li><a class="gp-link-list" href="">Careers</a></li>
            <li><a class="gp-link-list" href="">Greenline Partner Solutions</a></li>
            <li><a class="gp-link-list" href="">Privacy / Cookies</a></li>
            </ul>
          </div>
          <div class="gp-footer-grid gp-col-2 eb2416">
            <ul class="op-list c655">
              <li><a class="gp-link-list" href="">Terms and conditions / Security </a></li>
              <li><a class="gp-link-list" href="">Top destinations</a></li>
              <li><a class="gp-link-list" href="">Stations</a></li>
              <li><a class="gp-link-list" href="">Contact</a></li>
            </ul>
          </div>
         </div>
        </div> 
      </div>
      <div class="row gp-footer-2 eb2218">
        <div class="cell gp-footer-container eb1425">
          <div class="gp-item-row">
            <div class="gp-footer-grid gp-left-footer eb7878">
              <p>Copyright © 2021 Greenline.com Limited and its affiliated companies. All rights reserved.</p>
            </div>
            <div class="gp-footer-grid gp-right-footer eb7878">
              <a class="op-bottom-link" href="">Link 1</a>
              <a class="op-bottom-link" href="">Link 2</a>
            </div>
          </div>
        </div>
      </div>
    </div>`
  });

  toAdd('block-logo') && bm.add('block-logo', {
    category: 'Components',
    label: 'Logo Grid',
    attributes: { class: 'ca ca-logogrid' },
    content: `<div class="row gp-album">
        <div class="cell gp-album-container">
         <div class="gp-logo-row">
          <div class="gp-logo-grid gplogo">
            <div class="gp-grid-wrapp">
              <figure class="gp-figure">
                <a href=""><img src="https://via.placeholder.com/800X550/55595c/FFFFFFF/?text=Thumbnail" alt="" /></a>
              </figure>
            </div> 
          </div>
          <div class="gp-logo-grid gplogo">
            <div class="gp-grid-wrapp">
              <figure class="gp-figure">
                <a href=""><img src="https://via.placeholder.com/800X550/55595c/FFFFFFF/?text=Thumbnail" alt="" /></a>
              </figure>
            </div> 
          </div>
          <div class="gp-logo-grid gplogo">
            <div class="gp-grid-wrapp">
              <figure class="gp-figure">
              <a href=""><img src="https://via.placeholder.com/800X550/55595c/FFFFFFF/?text=Thumbnail" alt="" /></a>
              </figure>
            </div> 
          </div>
          <div class="gp-logo-grid gplogo">
            <div class="gp-grid-wrapp">
              <figure class="gp-figure">
              <a href=""><img src="https://via.placeholder.com/800X550/55595c/FFFFFFF/?text=Thumbnail" alt="" /></a>
              </figure>
            </div> 
          </div>
          <div class="gp-logo-grid gplogo">
            <div class="gp-grid-wrapp">
              <figure class="gp-figure">
              <a href=""><img src="https://via.placeholder.com/800X550/55595c/FFFFFFF/?text=Thumbnail" alt="" /></a>
              </figure>
            </div> 
          </div>
         </div>
        </div> 
      </div>`
  });
}
