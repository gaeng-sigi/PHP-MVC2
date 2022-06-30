(function () {
    // 피드 올릴때 뜨는 창.(모달)
    const btnNewFeedModal = document.querySelector('#btnNewFeedModal');
    if(btnNewFeedModal) {
        const modal = document.querySelector('#newFeedModal');
        const body =  modal.querySelector('#id-modal-body');
        const frmElem = modal.querySelector('form');
        const btnClose = modal.querySelector('.btn-close');
        //이미지 값이 변하면
        frmElem.imgs.addEventListener('change', function(e) {
            console.log(`length: ${e.target.files.length}`);
            if(e.target.files.length > 0) {
                body.innerHTML = `
                    <div>
                        <div class="d-flex flex-md-row">
                            <div class="flex-grow-1 h-full"><img id="id-img" class="w300"></div>
                            <div class="ms-1 w250 d-flex flex-column">                
                                <textarea placeholder="문구 입력..." class="flex-grow-1 p-1"></textarea>
                                <input type="text" placeholder="위치" class="mt-1 p-1">
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-primary">공유하기</button>
                    </div>
                `;
                const imgElem = body.querySelector('#id-img');

                const imgSource = e.target.files[0];
                const reader = new FileReader();
                reader.readAsDataURL(imgSource);
                reader.onload = function() {
                    imgElem.src = reader.result;
                };

                const shareBtnElem = body.querySelector('button');
                shareBtnElem.addEventListener('click', function() {
                    const files = frmElem.imgs.files;

                    const fData = new FormData();
                    for(let i=0; i<files.length; i++) {
                        fData.append('imgs[]', files[i]);
                    }
                    fData.append('ctnt', body.querySelector('textarea').value);
                    fData.append('location', body.querySelector('input[type=text]').value);

                    fetch('/feed/rest', {
                        method: 'post',
                        body: fData                       
                    }).then(res => res.json())
                        .then(myJson => {
                            console.log(myJson);

                            if(myJson.result) {                                
                                btnClose.click();
                            }
                        });
                        
                });
            }
        });

        // 피드 올리는 아이콘 클릭 시 뜨는 내용.
        btnNewFeedModal.addEventListener('click', function() {
            const selFromComBtn = document.createElement('button');
            selFromComBtn.type = 'button';
            selFromComBtn.className = 'btn btn-primary';
            selFromComBtn.innerText = '컴퓨터에서 선택';            
            selFromComBtn.addEventListener('click', function() {
                frmElem.imgs.click();
            });
            body.innerHTML = null;
            body.appendChild(selFromComBtn);
        });
    }

    function moveToFeedWin(iuser) {
        location.href = `/user/feedwin?iuser=${iuser}`;
    }
    
    // 피드 사진
    const feedObj = {
        limit: 20,
        itemLength: 0,
        currentPage: 1,
        swiper: null,
        loadingElem: document.querySelector('.loading'),
        containerElem: document.querySelector('#item_container'),
        getFeedList: function() {
            this.showLoading();            
            const param = {
                page: this.currentPage++
            }
            fetch('/feed/rest' + encodeQueryString(param))
            .then(res => res.json())
            .then(list => {                
                this.makeFeedList(list);                
            })
            .catch(e => {
                console.error(e);
                this.hideLoading();
            });
        },
        makeFeedList: function(list) {
            if(list.length !== 0) {
                list.forEach(item => {
                    const divItem = this.makeFeedItem(item);
                    this.containerElem.appendChild(divItem);
                });
            }

            if (this.swiper !== null) { this.swiper = null; }
            this.swiper = new Swiper(".swiper", {
                navigation: {
                    prevEl: ".swiper-button-prev",
                    nextEl: ".swiper-button-next",
                },
                pagination: { el: ".swiper-pagination" },
                allowTouchMove: false,
                direction: "horizontal",
                loop: false,
            });

            this.hideLoading();
        },
        makeFeedItem: function(item) {
            console.log(item);
            const divContainer = document.createElement('div');
            divContainer.className = 'item mt-3 mb-3';
            
            const divTop = document.createElement('div');
            divContainer.appendChild(divTop);

            const regDtInfo = getDateTimeInfo(item.regdt);
            divTop.className = 'd-flex flex-row ps-3 pe-3';
            const writerImg = `<img src='/static/img/profile/${item.iuser}/${item.mainimg}' 
                onerror='this.error=null;this.src="/static/img/profile/defaultProfileImg_100.png"'>`;

            divTop.innerHTML = `
                <div class="d-flex flex-column justify-content-center">
                    <div class="circleimg h40 w40 pointer feedwin">${writerImg}</div>
                </div>
                <div class="p-3 flex-grow-1">
                    <div><span class="feedwin">${item.writer}</span> - ${regDtInfo}</div>
                    <div>${item.location === null ? '' : item.location}</div>
                </div>
            `;

            const feedWinList = divTop.querySelectorAll('.feedwin');
            feedWinList.forEach(el => {
                el.addEventListener("click", () => {
                    moveToFeedWin(item.iuser);
                });
            });

            const divImgSwiper = document.createElement('div');
            divContainer.appendChild(divImgSwiper);
            divImgSwiper.className = 'swiper item_img';
            divImgSwiper.innerHTML = `
                <div class="swiper-wrapper align-items-center"></div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            `;
            const divSwiperWrapper = divImgSwiper.querySelector('.swiper-wrapper');
            
            //TODO : imgList forEach 돌릴 예정
            item.imgList.forEach(function(imgObj) {
                const divSwiperSlide = document.createElement('div');
                divSwiperWrapper.appendChild(divSwiperSlide);
                divSwiperSlide.classList.add('swiper-slide');
    
                const img = document.createElement('img');
                divSwiperSlide.appendChild(img);
                img.className = 'w100p_mw614';
                img.src = `/static/img/feed/${item.ifeed}/${imgObj.img}`;
            });

            // 
            const divBtns = document.createElement('div');
            divContainer.appendChild(divBtns);
            divBtns.className = 'favCont p-3 d-flex flex-row';

            // 피드 안 좋아요 아이콘.
            const heartIcon = document.createElement('i');
            divBtns.appendChild(heartIcon);
            heartIcon.className = 'fa-heart pointer rem1_5 me-3';
            heartIcon.classList.add(item.isFav === 1 ? 'fas' : 'far');
            heartIcon.addEventListener('click', e => {

                let method = 'POST';
                if (item.isFav === 1) { // delete (1은 0으로 바꿔줘야 함)
                    method = 'DELETE';
                }

                fetch(`/feed/fav/${item.ifeed}`, {
                    'method': method,
                }).then(res => res.json())
                    .then(res => {
                        if (res.result) {
                            item.isFav = 1 - item.isFav;
                            if (item.isFav === 0) { // 좋아요 취소
                                heartIcon.classList.remove('fas');
                                heartIcon.classList.add('far');
                            } else { // 좋아요 체크
                                heartIcon.classList.remove('far');
                                heartIcon.classList.add('fas');
                            }
                        } else {
                            alert('좋아요를 할 수 없습니다.');
                        }
                    })
                    .catch(e => {
                        alert('네트워크에 이상이 있습니다.');
                    });
            });

            // 피드 안 다이렉트 메세지.
            const divDm = document.createElement('div');
            divBtns.appendChild(divDm);
            divDm.className = 'pointer';
            divDm.innerHTML = `<svg aria-label="다이렉트 메세지" class="_8-yf5 " color="#262626" fill="#262626" height="24" role="img" viewBox="0 0 24 24" width="24"><line fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2" x1="22" x2="9.218" y1="3" y2="10.083"></line><polygon fill="none" points="11.698 20.334 22 3.001 2 3.001 9.218 10.084 11.698 20.334" stroke="currentColor" stroke-linejoin="round" stroke-width="2"></polygon></svg>`;
            
            // 피드 안 좋아요 개수.
            const divFav = document.createElement('div');
            divContainer.appendChild(divFav);
            divFav.className = 'p-3 d-none';

            const spanFavCnt = document.createElement('span');
            divFav.appendChild(spanFavCnt);
            spanFavCnt.className = 'bold';
            spanFavCnt.innerHTML = `좋아요 ${item.favCnt}개`;

            if(item.favCnt > 0) { divFav.classList.remove('d-none') };
            
            // 피드 안 내용.
            if (item.ctnt !== null && item.ctnt !== '') {
                const divCtnt = document.createElement('div');
                divContainer.appendChild(divCtnt);
                divCtnt.innerText = item.ctnt;
                divCtnt.className = 'itemCtnt p-3';
            }

            const divCmt = document.createElement('div');
            divContainer.appendChild(divCmt);
            
            const divCmtList = document.createElement('div');
            divContainer.appendChild(divCmtList);
            
            const divCmtForm = document.createElement('div');
            divCmt.appendChild(divCmtForm);
            divCmtForm.className = "d-flex flex-row";

            divCmtForm.innerHTML = `
                <input type="text" class="flex-grow-1 my_input back_color p-2" placeholder="댓글을 입력하세요.">
                <button type="button" class="btn btn-outline-primary">등록</button>
            `;

            return divContainer;
        },

        showLoading: function() { this.loadingElem.classList.remove('d-none'); },
        hideLoading: function() { this.loadingElem.classList.add('d-none'); }

    }
    feedObj.getFeedList();

})();